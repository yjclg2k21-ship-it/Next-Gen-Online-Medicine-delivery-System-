<?php
namespace App\Services;

/**
 * CSV Export Service
 * Rapid generation of CSV reports for admin analytics processing.
 */
class CsvExportService {

    public static function download($filename, $headers, $data) {
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="' . $filename . '.csv"');
        
        $output = fopen('php://output', 'w');
        fputcsv($output, $headers);
        
        foreach ($data as $row) {
            fputcsv($output, $row);
        }
        fclose($output);
        exit;
    }
}
