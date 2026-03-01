<?php

namespace App\Services;

use App\Models\Proposal;
use Carbon\Carbon;
use PhpOffice\PhpWord\TemplateProcessor;

class ProposalGeneratorService
{
    /**
     * Generate a "Pengajuan Kegiatan Bulanan" document from Proposal->content.
     *
     * @param  Proposal  $proposal  The proposal whose content array drives the document.
     * @return string The relative file path within the public disk.
     */
    public function generate(Proposal $proposal): string
    {
        $templatePath = storage_path('app/templates/template_pengajuan_primdev.docx');

        if (! file_exists($templatePath)) {
            throw new \RuntimeException('Template file not found at: '.$templatePath);
        }

        $templateProcessor = new TemplateProcessor($templatePath);

        // --- Static Variables ---
        $carbonDate = Carbon::createFromDate($proposal->year, $proposal->month, 1);
        $bulanUpper = strtoupper($carbonDate->translatedFormat('F'));

        $templateProcessor->setValue('BULAN_UPPER', $bulanUpper);
        $templateProcessor->setValue('TAHUN', (string) $proposal->year);
        $templateProcessor->setValue('TGL_SEKARANG', Carbon::now()->translatedFormat('d F Y'));

        // --- Dynamic Data from Proposal->content (No Loop) ---
        $item = $proposal->content ?? [];

        // Backward compatibility for old proposals that used Repeater (array of arrays with UUID keys)
        if (is_array($item) && count($item) > 0) {
            $firstElement = reset($item);
            if (is_array($firstElement)) {
                $item = $firstElement;
            }
        }

        if (! empty($item)) {
            // Clone block exactly once, stripping tags without numbering inner variables
            $templateProcessor->cloneBlock('block_kegiatan', 1, true, false);

            $templateProcessor->setValue("nama_kegiatan", $item['name'] ?? '-');
            $templateProcessor->setValue("tgl_kegiatan", $item['date'] ?? '-');
            $templateProcessor->setValue("lok_kegiatan", $item['location'] ?? '-');

            // Support multiline descriptions via XML line breaks
            $cleanDesc = preg_replace('~\R~u', '</w:t><w:br/><w:t>', strip_tags($item['description'] ?? '-'));
            $templateProcessor->setValue("deskripsi_lengkap", $cleanDesc);
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

        $monthPadded = str_pad($proposal->month, 2, '0', STR_PAD_LEFT);
        $filename = "Pengajuan_PrimDev_{$monthPadded}_{$proposal->year}.docx";
        $fullPath = $outputDir.DIRECTORY_SEPARATOR.$filename;

        $templateProcessor->saveAs($fullPath);

        // Update Proposal file_path
        $proposal->update(['file_path' => 'proposals/'.$filename]);

        return 'proposals/'.$filename;
    }
}
