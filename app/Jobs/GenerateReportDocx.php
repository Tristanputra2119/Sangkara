<?php

namespace App\Jobs;

use App\Models\Report;
use App\Models\Transaction;
use App\Models\Meeting;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use PhpOffice\PhpWord\TemplateProcessor;
use Illuminate\Support\Facades\Storage;
use Throwable;

class GenerateReportDocx implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public Report $report;

    /**
     * Create a new job instance.
     */
    public function __construct(Report $report)
    {
        $this->report = $report;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
            $this->report->update(['status' => 'processing']);

            // Fetch data
            $startDate = now()->setDate($this->report->year, $this->report->month, 1)->startOfMonth();
            $endDate = $startDate->copy()->endOfMonth();

            $transactions = Transaction::with(['user', 'category'])
                ->whereBetween('transaction_date', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')])
                ->get();

            $meetings = Meeting::whereBetween('meeting_date', [$startDate, $endDate])->get();

            // Load Template
            $templatePath = storage_path('app/report_template.docx');

            if (!file_exists($templatePath)) {
                // If template missing, create an empty php word to save simple error
                throw new \Exception('Template file report_template.docx not found in storage/app.');
            }

            $templateProcessor = new TemplateProcessor($templatePath);

            $templateProcessor->setValue('month', $startDate->format('F'));
            $templateProcessor->setValue('year', $this->report->year);

            // Clone rows for transactions
            $templateProcessor->cloneRow('t_date', $transactions->count() > 0 ? $transactions->count() : 1);
            $tIndex = 1;
            if ($transactions->count() == 0) {
                $templateProcessor->setValue('t_date#1', '-');
                $templateProcessor->setValue('t_desc#1', 'No transactions');
                $templateProcessor->setValue('t_amt#1', '-');
            } else {
                foreach ($transactions as $transaction) {
                    $templateProcessor->setValue('t_date#' . $tIndex, $transaction->transaction_date->format('Y-m-d'));
                    $templateProcessor->setValue('t_desc#' . $tIndex, $transaction->description ?? ($transaction->category ? $transaction->category->name : ''));
                    // Use raw integer formatting
                    $templateProcessor->setValue('t_amt#' . $tIndex, 'Rp ' . number_format($transaction->amount, 0, ',', '.'));
                    $tIndex++;
                }
            }

            // Clone rows for meetings
            $templateProcessor->cloneRow('m_date', $meetings->count() > 0 ? $meetings->count() : 1);
            $mIndex = 1;
            if ($meetings->count() == 0) {
                $templateProcessor->setValue('m_date#1', '-');
                $templateProcessor->setValue('m_title#1', 'No meetings');
            } else {
                foreach ($meetings as $meeting) {
                    $templateProcessor->setValue('m_date#' . $mIndex, $meeting->meeting_date->format('Y-m-d H:i'));
                    $templateProcessor->setValue('m_title#' . $mIndex, $meeting->title);
                    $mIndex++;
                }
            }

            // Save the file
            $filename = 'report_' . $this->report->year . '_' . str_pad($this->report->month, 2, '0', STR_PAD_LEFT) . '_' . time() . '.docx';
            $publicPath = 'reports/' . $filename;
            
            $fullSavePath = storage_path('app/public/' . $publicPath);
            
            if (!file_exists(dirname($fullSavePath))) {
                mkdir(dirname($fullSavePath), 0755, true);
            }

            $templateProcessor->saveAs($fullSavePath);

            $this->report->update([
                'status' => 'finalized',
                'file_path' => $publicPath,
            ]);

        } catch (Throwable $e) {
            $this->report->update([
                'status' => 'failed',
                'error_message' => $e->getMessage(),
            ]);
        }
    }
}
