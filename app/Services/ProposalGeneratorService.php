<?php

namespace App\Services;

use App\Models\Report;
use Carbon\Carbon;
use PhpOffice\PhpWord\TemplateProcessor;

class ProposalGeneratorService
{
    /**
     * Generate a "Pengajuan Kegiatan Bulanan" document from Report->content.
     *
     * @param  Report  $report  The report whose content array drives the document.
     * @return string The relative file path within the public disk.
     */
    public function generate(Report $report): string
    {
        $templatePath = storage_path('app/templates/template_pengajuan_primdev.docx');

        if (! file_exists($templatePath)) {
            throw new \RuntimeException('Template file not found at: '.$templatePath);
        }

        $templateProcessor = new TemplateProcessor($templatePath);

        // --- Static Variables ---
        $carbonDate = Carbon::createFromDate($report->year, $report->month, 1);
        $bulanUpper = strtoupper($carbonDate->translatedFormat('F'));

        $templateProcessor->setValue('BULAN_UPPER', $bulanUpper);
        $templateProcessor->setValue('TAHUN', (string) $report->year);
        $templateProcessor->setValue('TGL_SEKARANG', Carbon::now()->translatedFormat('d F Y'));

        // --- Dynamic List from Report->content (cloneBlock) ---
        $items = $report->content ?? [];

        if (! empty($items)) {
            $templateProcessor->cloneBlock('block_kegiatan', count($items), true, true);

            foreach ($items as $index => $item) {
                $i = $index + 1;

                $templateProcessor->setValue("nama_kegiatan#$i", $item['name'] ?? '-');
                $templateProcessor->setValue("tgl_kegiatan#$i", $item['date'] ?? '-');
                $templateProcessor->setValue("lok_kegiatan#$i", $item['location'] ?? '-');

                // Support multiline descriptions via XML line breaks
                $cleanDesc = preg_replace('~\R~u', '</w:t><w:br/><w:t>', $item['description'] ?? '-');
                $templateProcessor->setValue("deskripsi_lengkap#$i", $cleanDesc);
            }
        } else {
            $templateProcessor->deleteBlock('block_kegiatan');
        }

        // --- Signature Variables (Static / Hardcoded) ---
        $templateProcessor->setValue('KETUA_NAMA', 'Made Ngurah Tristan Putra');
        $templateProcessor->setValue('KETUA_NIM', '2401020047');
        $templateProcessor->setValue('SEKRE_NAMA', 'I Putu Krisna Ariwidnyana');
        $templateProcessor->setValue('SEKRE_NIM', '2401020004');

        // --- Save Output ---
        $outputDir = storage_path('app/public/proposals');
        if (! file_exists($outputDir)) {
            mkdir($outputDir, 0755, true);
        }

        $monthPadded = str_pad($report->month, 2, '0', STR_PAD_LEFT);
        $filename = "Pengajuan_PrimDev_{$monthPadded}_{$report->year}.docx";
        $fullPath = $outputDir.DIRECTORY_SEPARATOR.$filename;

        $templateProcessor->saveAs($fullPath);

        // Update Report file_path
        $report->update(['file_path' => 'proposals/'.$filename]);

        return 'proposals/'.$filename;
    }
}
