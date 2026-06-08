<?php
namespace App\Middleware {
    class AuthGuard {
        public static function handle() {}
        public static function getUser() {
            return ['id' => 5, 'name' => 'Test User', 'role' => 'user'];
        }
    }
}

namespace {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);

    // Manual Autoloader Simulation
    spl_autoload_register(function ($class) {
        $prefix = 'App\\';
        $base_dir = __DIR__ . '/backend/app/';
        $len = strlen($prefix);
        if (strncmp($prefix, $class, $len) !== 0) return;
        $relative_class = substr($class, $len);
        $file = $base_dir . str_replace('\\', '/', $relative_class) . '.php';
        if (file_exists($file)) require_once $file;
    });

    use App\Controllers\OrderController;
    
    class DiagnosticController extends OrderController {
        protected function getPostData() {
            return [
                'address_id' => 1,
                'delivery_method_id' => 1,
                'payment_method' => 'card',
                'prescription_id' => 0
            ];
        }
        /** @return mixed */
        public function testStore() {
            return $this->store();
        }
    }

    try {
        echo "Starting Diagnostic Run...\n";
        $ctrl = new DiagnosticController();
        $res = $ctrl->testStore();
        echo "\nRESULT: " . print_r($res, true) . "\n";
    } catch (Throwable $e) {
        echo "\n!!! CRASH DETECTED !!!\n";
        echo "Message: " . $e->getMessage() . "\n";
        echo "File: " . $e->getFile() . "\n";
        echo "Line: " . $e->getLine() . "\n";
        echo "Trace: " . $e->getTraceAsString() . "\n";
    }
}
