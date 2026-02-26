<?php

use PhpOffice\PhpWord\IOFactory;
use PhpOffice\PhpWord\Settings;

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$docxPath = storage_path('app/templates/template_pengajuan_primdev.docx');
$pdfPath = storage_path('app/test_output.pdf');

if (!file_exists($docxPath)) {
    echo "Template not found.\n";
    exit(1);
}

try {
    // Set PDF renderer
    $domPdfPath = base_path('vendor/dompdf/dompdf');
    Settings::setPdfRendererPath($domPdfPath);
    Settings::setPdfRendererName(Settings::PDF_RENDERER_DOMPDF);

    // Load DOCX
    $phpWord = IOFactory::load($docxPath, 'Word2007');

    // Save PDF
    $pdfWriter = IOFactory::createWriter($phpWord, 'PDF');
    $pdfWriter->save($pdfPath);

    echo "Saved to: $pdfPath\n";
} catch (\Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
