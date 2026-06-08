<?php
/**
 * AI Verification Script (Mediflow OCR Logic)
 * Restored from legacy project structure.
 * 
 * Tests the environment and AI readiness for prescription processing.
 */
require_once __DIR__ . '/Harness.php';
use Tests\Harness;

try {
    Harness::init();
    echo "====================================\n";
    echo "   AI Integration Verification      \n";
    echo "====================================\n\n";

    // 1. Verify Configuration Loading
    $config = require __DIR__ . '/../config/app.php';
    Harness::assertTrue(isset($config['app_name']), 'Application config is loadable.');

    // 2. Check Gemini Key
    if (empty(getenv('GEMINI_API_KEY')) && empty($config['gemini_api_key'])) {
        echo "[WARN] GEMINI_API_KEY is missing. OCR will run in simulation mode.\n";
    } else {
        echo "[PASS] AI Core Key is configured.\n";
    }

    // 3. System readiness
    Harness::assertTrue(class_exists('\App\Controllers\PrescriptionController'), 'Prescription module is loaded.');

    echo "\n=> AI Verification Completed Successfully.\n";

} catch (\Exception $e) {
    echo "\n[ERROR] Verification Failed: " . $e->getMessage() . "\n";
    exit(1);
}
