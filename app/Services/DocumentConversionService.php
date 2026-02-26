<?php

namespace App\Services;

use PhpOffice\PhpWord\IOFactory;
use PhpOffice\PhpWord\Settings;
use RuntimeException;

class DocumentConversionService
{
    /**
     * Convert a `.docx` file to `.pdf` using PhpWord and DomPDF.
     *
     * @param string $docxFullPath The absolute path to the source .docx file.
     * @return string The absolute path to the generated .pdf file.
     * @throws RuntimeException
     */
    public function convertDocxToPdf(string $docxFullPath): string
    {
        if (!file_exists($docxFullPath)) {
            throw new RuntimeException("Berks .docx tidak ditemukan di: {$docxFullPath}");
        }

        // Tentukan path dompdf di dalam vendor/
        $domPdfPath = base_path('vendor/dompdf/dompdf');
        
        if (!is_dir($domPdfPath)) {
            throw new RuntimeException("Library dompdf tidak ditemukan. Pastikan package terinstall.");
        }

        // Set pengaturan PDF Renderer PhpWord
        Settings::setPdfRendererPath($domPdfPath);
        Settings::setPdfRendererName(Settings::PDF_RENDERER_DOMPDF);

        // Load dokumen DOCX
        $phpWord = IOFactory::load($docxFullPath, 'Word2007');

        // Prepare PDF path
        $pdfFullPath = str_replace('.docx', '.pdf', $docxFullPath);

        // Simpan sebagai PDF
        $pdfWriter = IOFactory::createWriter($phpWord, 'PDF');
        $pdfWriter->save($pdfFullPath);

        if (!file_exists($pdfFullPath)) {
            throw new RuntimeException("Gagal menggenerate file PDF.");
        }

        return $pdfFullPath;
    }
}
