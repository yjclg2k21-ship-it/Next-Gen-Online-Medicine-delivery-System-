<?php
namespace App\Services;

/**
 * Prescription Processor Service
 * Integrates simulated OCR technology to parse handwritten/digital prescriptions.
 */
class PrescriptionProcessor {
    
    /**
     * Extracts text and maps it to the medicine catalog.
     * @param string $filePath
     * @return array extracted data
     */
    public function processDocument($filePath) {
        // Simulate an OCR delay
        // sleep(1);

        // Dummy processing output
        return [
            'confidence' => 0.88,
            'medicines_found' => [
                ['name' => 'Paracetamol', 'dosage' => '650mg', 'frequency' => '1-0-1', 'duration' => '5 days'],
                ['name' => 'Amoxicillin', 'dosage' => '500mg', 'frequency' => '1-0-1', 'duration' => '5 days'],
            ],
            'doctor_details' => [
                'name' => 'Dr. R.K. Sharma',
                'reg_no' => 'MCI-19234'
            ],
            'flags' => ['Handwriting clarity medium']
        ];
    }
}
