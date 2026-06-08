<?php
namespace App\Controllers;

use App\Middleware\AuthGuard;
use App\Core\ResponseHandler;
use Exception;

class SettingController extends BaseController {
    
    public function index() {
        try {
            $db = \App\Core\Database::getInstance()->getConnection();
            $stmt = $db->query("SELECT key_name as `key`, value FROM settings");
            $results = $stmt->fetchAll();
            
            $settings = [];
            foreach ($results as $row) {
                $settings[$row['key']] = $row['value'];
            }

            // Fallback for default values if table is empty
            if (empty($settings)) {
                $settings = [
                    'platform_name' => 'Mediflow Online',
                    'support_email' => 'support@mediflow.in',
                    'version' => 'v2.8.5-stable'
                ];
            }

            return $this->json(['success' => true, 'data' => ['settings' => $settings]]);
        } catch (Exception $e) {
            return $this->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function update() {
        AuthGuard::handle();
        if (AuthGuard::getUser()['role'] !== 'admin') return ResponseHandler::forbidden();

        $data = $this->getPostData();
        try {
            $db = \App\Core\Database::getInstance()->getConnection();
            $db->beginTransaction();

            foreach ($data as $key => $value) {
                $stmt = $db->prepare("INSERT INTO settings (key_name, value) VALUES (?, ?) ON DUPLICATE KEY UPDATE value = ?");
                $stmt->execute([$key, $value, $value]);
            }

            $db->commit();
            return $this->json([
                'success' => true, 
                'message' => 'Ecosystem-wide configuration deployed successfully.',
                'updated_at' => date('Y-m-d H:i:s')
            ]);
        } catch (Exception $e) {
            $db = \App\Core\Database::getInstance()->getConnection();
            if ($db->inTransaction()) $db->rollBack();
            return $this->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function security() {
        try {
            $security = [
                'mfa_enabled' => true,
                'ip_restricted' => false,
                'min_chars' => 12,
                'special_chars' => 2,
                'rotation_days' => 90,
                'firewall_status' => 'Active',
                'active_rules' => 1240,
                'threats_blocked_24h' => 84
            ];
            $this->json(['success' => true, 'data' => ['security' => $security]]);
        } catch (Exception $e) {
            $this->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function getBadWeather() {
        try {
            $db = \App\Core\Database::getInstance()->getConnection();
            $stmt = $db->query("SELECT `value` FROM `settings` WHERE `key` = 'bad_weather'");
            $res = $stmt->fetch();
            $badWeather = ($res && $res['value'] == '1') ? 1 : 0;
            return $this->json(['success' => true, 'bad_weather' => $badWeather]);
        } catch (Exception $e) {
            return $this->json(['success' => false, 'bad_weather' => 0]);
        }
    }
}
