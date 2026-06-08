<?php
/**
 * MediMitra PDF Proxy — Forces correct filename from server-side headers
 */
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['pdf_data'])) {
    // Robustly strip any data URI prefix (e.g. data:application/pdf;base64,)
    $pdfDataRaw = $_POST['pdf_data'];
    $pdfDataClean = preg_replace('/^data:application\/pdf;base64,/', '', $pdfDataRaw);
    $pdfData = base64_decode($pdfDataClean);
    $fileName = $_POST['filename'] ?? 'MediMitra_Invoice.pdf';

    header('Content-Type: application/pdf');
    header('Content-Disposition: attachment; filename="' . $fileName . '"');
    header('Content-Length: ' . strlen($pdfData));
    
    echo $pdfData;
    exit;
}
echo "Invalid Request";
