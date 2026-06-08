<?php
namespace Tests;

/**
 * Base Testing Harness for Mediflow
 * Restored from legacy project structure.
 * 
 * Provides basic testing utilities and bootstraps the application context.
 */
class Harness {
    public static function init() {
        // Load the core backend environment for testing
        require_once __DIR__ . '/../app/bootstrap.php';
        echo "[INFO] Test Harness Initialized.\n";
    }

    public static function assertTrue($condition, $message = '') {
        if (!$condition) {
            throw new \Exception("Assertion Failed: " . $message);
        }
        echo "[PASS] " . $message . "\n";
    }

    public static function assertEqual($expected, $actual, $message = '') {
        if ($expected !== $actual) {
            throw new \Exception("Assertion Failed: $message (Expected: $expected, Got: $actual)");
        }
        echo "[PASS] " . $message . "\n";
    }
}

// Ensure it can be run directly if needed
if (php_sapi_name() === 'cli' && basename(__FILE__) === basename($_SERVER['PHP_SELF'])) {
    Harness::init();
}
