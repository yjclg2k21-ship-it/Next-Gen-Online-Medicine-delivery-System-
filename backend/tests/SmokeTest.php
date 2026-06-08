<?php
/**
 * MediMitra Smoke Test Harness
 * Ensures that the core routing structures, controllers, and framework classes
 * do not possess syntax errors or structural load failures.
 */

// Basic autoload bridging
spl_autoload_register(function ($class) {
    if (strpos($class, 'App\\') === 0) {
        $file = __DIR__ . '/../app/' . str_replace('\\', '/', substr($class, 4)) . '.php';
        if (file_exists($file)) require $file;
    }
});

echo "Starting MediMitra Smoke Initialization Tests...\n";

// 1. Controller Mapping Smoke Test
$controllers = [
    '\\App\\Controllers\\AuthController',
    '\\App\\Controllers\\PaymentController',
    '\\App\\Controllers\\AdminController',
    '\\App\\Controllers\\DeliveryController',
    '\\App\\Controllers\\PharmacyInventoryController'
];

$failed = false;

foreach ($controllers as $actor) {
    try {
        if (!class_exists($actor)) {
            echo "[FAIL] Controller not found: $actor\n";
            $failed = true;
        } else {
            echo "[OK] Controller Resolved: $actor\n";
        }
    } catch (\Throwable $e) {
        echo "[CRITICAL FAIL] Exception loading $actor : " . $e->getMessage() . "\n";
        $failed = true;
    }
}

// 2. Syntax Validation on specific routing paths (Parsing Check)
echo "\nChecking API mappings integrity...\n";
ob_start();
try {
    // We mock the core routing engine locally
    class MockRouter {
        public $routes = [];
        public function add($method, $path, $handler) {
            $this->routes[] = compact('method', 'path', 'handler');
        }
    }
    $router = new MockRouter();
    
    // Attempting to include the API file
    if (!file_exists(__DIR__ . '/../routes/api.php')) {
        echo "[FAIL] Core routing rules missing.\n";
        $failed = true;
    } else {
        require __DIR__ . '/../routes/api.php';
        if (count($router->routes) > 20) {
            echo "[OK] Successfully mapped " . count($router->routes) . " internal application rules.\n";
        } else {
            echo "[WARNING] Sparse API route count.\n";
        }
    }
} catch (\Throwable $e) {
    echo "[FAIL] Routing parse exception: " . $e->getMessage() . "\n";
    $failed = true;
}
ob_end_clean();

// 3. Status Report
echo "\n========== SMOKE TEST RESULTS ==========\n";
if ($failed) {
    echo "STATUS: PIPELINE FAILED\n";
    exit(1);
} else {
    echo "STATUS: ALL SYSTEMS NOMINAL\n";
    exit(0);
}
