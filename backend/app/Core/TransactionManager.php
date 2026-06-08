<?php
namespace App\Core;

/**
 * TransactionManager
 * Ensures atomic DB operations - no partial failures.
 */
class TransactionManager {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    public function run(callable $callback) {
        try {
            $this->db->beginTransaction();
            $result = $callback($this->db);
            $this->db->commit();
            return $result;
        } catch (\Exception $e) {
            $this->db->rollBack();
            throw $e;
        }
    }
}
