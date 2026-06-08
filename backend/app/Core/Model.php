<?php
namespace App\Core;

/**
 * Base Model
 * Abstract class providing foundational global database access methods (ORM logic).
 */
abstract class Model {
    protected $table;
    protected $db;
    protected $useSoftDelete = false;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    /**
     * Fetch all records from the table
     * 
     * @return array[] A list of all records.
     */
    public function findAll() {
        $sql = "SELECT * FROM `{$this->table}`";
        if ($this->useSoftDelete) {
            $sql .= " WHERE deleted_at IS NULL";
        }
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll();
    }

    /**
     * Find a record by ID
     * 
     * @param mixed $id The unique identifier of the record.
     * @return array|false The record as an associative array, or false if not found.
     */
    public function findById($id) {
        $sql = "SELECT * FROM `{$this->table}` WHERE id = ?";
        if ($this->useSoftDelete) {
            $sql .= " AND deleted_at IS NULL";
        }
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    /**
     * Generic Insert
     */
    public function create($data) {
        $keys = array_keys($data);
        $fields = implode('`, `', $keys);
        $placeholders = implode(', ', array_fill(0, count($keys), '?'));
        
        $sql = "INSERT INTO `{$this->table}` (`{$fields}`) VALUES ({$placeholders})";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(array_values($data));
        return $this->db->lastInsertId();
    }

    /**
     * Generic Update
     */
    public function update($id, $data) {
        $fields = "";
        foreach ($data as $key => $value) {
            $fields .= "`$key` = ?, ";
        }
        $fields = rtrim($fields, ", ");
        
        $sql = "UPDATE `{$this->table}` SET {$fields} WHERE id = ?";
        $values = array_values($data);
        $values[] = $id;
        
        $stmt = $this->db->prepare($sql);
        return $stmt->execute($values);
    }

    /**
     * Soft Delete
     */
    public function delete($id) {
        if ($this->useSoftDelete) {
            $stmt = $this->db->prepare("UPDATE `{$this->table}` SET deleted_at = NOW() WHERE id = ?");
            return $stmt->execute([$id]);
        } else {
            $stmt = $this->db->prepare("DELETE FROM `{$this->table}` WHERE id = ?");
            return $stmt->execute([$id]);
        }
    }

    /**
     * Count records with optional filter
     */
    public function count($where = '', $params = []) {
        $sql = "SELECT COUNT(*) FROM `{$this->table}`";
        if ($this->useSoftDelete) {
            $sql .= " WHERE deleted_at IS NULL";
            if ($where) $sql .= " AND ({$where})";
        } elseif ($where) {
            $sql .= " WHERE {$where}";
        }
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return (int)$stmt->fetchColumn();
    }

    /**
     * Sum a column with optional filter
     */
    public function sum($column, $where = '', $params = []) {
        $sql = "SELECT SUM(`$column`) FROM `{$this->table}`";
        if ($this->useSoftDelete) {
            $sql .= " WHERE deleted_at IS NULL";
            if ($where) $sql .= " AND ({$where})";
        } elseif ($where) {
            $sql .= " WHERE {$where}";
        }
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return (float)($stmt->fetchColumn() ?: 0);
    }

    /**
     * Find recent records
     * 
     * @param int $limit Maximum number of records to return.
     * @param string $orderBy Order by clause.
     * @return array[] A list of recent records.
     */
    public function findRecent($limit = 10, $orderBy = 'created_at DESC') {
        $sql = "SELECT * FROM `{$this->table}`";
        if ($this->useSoftDelete) {
            $sql .= " WHERE deleted_at IS NULL";
        }
        $sql .= " ORDER BY {$orderBy} LIMIT " . (int)$limit;
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll();
    }

    /**
     * Generic where clause
     * 
     * @param string $condition The SQL where condition (e.g. "status = ?").
     * @param array $params The parameters to bind to the condition.
     * @return array[] A list of matching records.
     */
    public function where($condition, $params = []) {
        $sql = "SELECT * FROM `{$this->table}` WHERE {$condition}";
        if ($this->useSoftDelete) {
            $sql .= " AND deleted_at IS NULL";
        }
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }
}
