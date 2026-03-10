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

        // --- Dynamic Data from Proposal->content ---
        $contentArray = $proposal->content ?? [];

        // Filter valid items
        $validItems = array_values(array_filter($contentArray, fn($item) => is_array($item)));

        if (!empty($validItems)) {
            $count = count($validItems);

            // Clone the block N times with indexed variables (nama_kegiatan#1, #2, ...)
            $templateProcessor->cloneBlock('block_kegiatan', $count, true, true);

            $values = [];
            foreach ($validItems as $i => $item) {
                $n = $i + 1;
                $values["nama_kegiatan#{$n}"] = "{$n}. " . ($item['name'] ?? '-');
                $values["tgl_kegiatan#{$n}"]  = isset($item['date'])
                    ? Carbon::parse($item['date'])->translatedFormat('l, j F Y')
                    : '-';
                $values["lok_kegiatan#{$n}"]      = $item['location'] ?? '-';
                $values["deskripsi_lengkap#{$n}"] = strip_tags($item['description'] ?? '-');
            }

            $templateProcessor->setValues($values);
        } else {
            $templateProcessor->deleteBlock('block_kegiatan');
        }

        // --- Signature Variables ---
        $templateProcessor->setValue('KETUA_NAMA', 'Made Ngurah Tristan Putra');
        $templateProcessor->setValue('KETUA_NIM', '2401020047');
        $templateProcessor->setValue('SEKRE_NAMA', 'I Putu Krisna Ariwidnyana');
        $templateProcessor->setValue('SEKRE_NIM', '2401020004');

        // --- Save Output ---
        $outputDir = storage_path('app/public/proposals');
        if (! file_exists($outputDir)) {
            mkdir($outputDir, 0755, true);
        }

        // Format: Pengajuan_Kegiatan_Bulan_Maret_2026.docx
        $bulanName = $carbonDate->translatedFormat('F');
        $filename = "Pengajuan_Kegiatan_Bulan_{$bulanName}_{$proposal->year}.docx";
        $fullPath = $outputDir.DIRECTORY_SEPARATOR.$filename;

        $templateProcessor->saveAs($fullPath);

        // Update Proposal file_path
        $proposal->update(['file_path' => 'proposals/'.$filename]);

        return 'proposals/'.$filename;
    }
}
