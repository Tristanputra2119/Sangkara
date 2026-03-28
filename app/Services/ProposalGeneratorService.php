<?php

namespace App\Services;

use App\Models\Proposal;
use Carbon\Carbon;
use PhpOffice\PhpWord\TemplateProcessor;

class ProposalGeneratorService
{
    /**
     * Helper to sanitize text for Word XML (so entities like &nbsp; or &ndash; don't break the docx).
     */
    private function sanitizeWordText(?string $text): string
    {
        if (empty($text)) {
            return '';
        }
        $decoded = html_entity_decode($text, ENT_QUOTES | ENT_HTML5, 'UTF-8');
        return htmlspecialchars($decoded, ENT_XML1 | ENT_QUOTES, 'UTF-8');
    }

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

        $templateProcessor->setValue('BULAN_UPPER', $this->sanitizeWordText($bulanUpper));
        $templateProcessor->setValue('TAHUN', $this->sanitizeWordText((string) $proposal->year));
        $templateProcessor->setValue('TGL_SEKARANG', $this->sanitizeWordText(Carbon::now()->translatedFormat('d F Y')));

        // --- Dynamic Data from Proposal->content ---
        $contentArray = $proposal->content ?? [];

        // Filter valid items
        $validItems = array_values(array_filter($contentArray, fn($item) => is_array($item)));

        if (!empty($validItems)) {
            $namaRun = new \PhpOffice\PhpWord\Element\TextRun();
            $tglRun = new \PhpOffice\PhpWord\Element\TextRun();
            $lokRun = new \PhpOffice\PhpWord\Element\TextRun();

            // Use a borderless table for description to create true paragraph breaks and avoid Justify stretching
            $descTable = new \PhpOffice\PhpWord\Element\Table([
                'borderSize'  => 0,
                'borderColor' => 'FFFFFF',
                'width'       => 100 * 50,
                'unit'        => 'pct',
                'cellMarginTop' => 0,
                'cellMarginBottom' => 0,
            ]);

            $fontOptions = ['name' => 'Calibri'];
            $boldFontOptions = ['name' => 'Calibri', 'bold' => true];

            foreach ($validItems as $i => $item) {
                if ($i > 0) {
                    $namaRun->addTextBreak();
                    $tglRun->addTextBreak();
                    $lokRun->addTextBreak();
                }
                
                $n = $i + 1;
                $name = $item['name'] ?? '-';
                $location = $item['location'] ?? '-';
                $desc = strip_tags($item['description'] ?? '-');

                // Add 3-space indentation to list items from index 1 onwards to align with item 0
                $prefix = ($i === 0) ? "{$n}. " : "   {$n}. ";

                $namaRun->addText($prefix . $this->sanitizeWordText($name), $fontOptions);
                
                $tglStr = isset($item['date']) ? Carbon::parse($item['date'])->translatedFormat('j F Y') : '-';
                $tglRun->addText($prefix . $this->sanitizeWordText($tglStr), $fontOptions);
                
                $lokRun->addText($prefix . $this->sanitizeWordText($location), $fontOptions);
                
                // For Description table, each item starts a new line on the left margin
                $descTitlePrefix = "{$n}. ";

                // For description: separate row for title and description
                $descTable->addRow();
                $descTable->addCell(10000)->addText(
                    $descTitlePrefix . $this->sanitizeWordText($name), 
                    $boldFontOptions, 
                    ['alignment' => 'left']
                );

                $descTable->addRow();
                $descTable->addCell(10000)->addText(
                    $this->sanitizeWordText($desc), 
                    $fontOptions, 
                    // Add indentation so all lines of the description align with the title text
                    ['alignment' => 'both', 'indentation' => ['left' => 360]] 
                );

                // Add empty row for spacing between items
                if ($i < count($validItems) - 1) {
                    $descTable->addRow();
                    $descTable->addCell(10000)->addText('', $fontOptions);
                }
            }

            $templateProcessor->setComplexValue('nama_kegiatan', $namaRun);
            $templateProcessor->setComplexValue('tgl_kegiatan', $tglRun);
            $templateProcessor->setComplexValue('lok_kegiatan', $lokRun);
            $templateProcessor->setComplexBlock('deskripsi_lengkap', $descTable);
        } else {
            $templateProcessor->setValue('nama_kegiatan', '-');
            $templateProcessor->setValue('tgl_kegiatan', '-');
            $templateProcessor->setValue('lok_kegiatan', '-');
            $templateProcessor->setValue('deskripsi_lengkap', '-');
        }

        // --- Signature Variables ---
        $templateProcessor->setValue('KETUA_NAMA', $this->sanitizeWordText('Made Ngurah Tristan Putra'));
        $templateProcessor->setValue('KETUA_NIM', $this->sanitizeWordText('2401020047'));
        $templateProcessor->setValue('SEKRE_NAMA', $this->sanitizeWordText('I Putu Krisna Ariwidnyana'));
        $templateProcessor->setValue('SEKRE_NIM', $this->sanitizeWordText('2401020004'));

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
