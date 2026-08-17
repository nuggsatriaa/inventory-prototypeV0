<?php

namespace App\Models;

use PDO;
use PDOException;

class Database
{
    private static $instance = null;

    public static function getConnection(): PDO
    {
        if (self::$instance === null) {
            try {
                // Perhatikan perubahannya pada charset=utf8mb4
                self::$instance = new PDO(
                    "mysql:host=localhost;dbname=db_inventory;charset=utf8mb4",
                    "root",
                    "",
                    [
                        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                        PDO::ATTR_EMULATE_PREPARES => false,
                        // Menyelaraskan encoding string saat koneksi dibuat
                        PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4 COLLATE utf8mb4_0900_ai_ci"
                    ]
                );
            } catch (PDOException $e) {
                die("Koneksi Database Gagal: " . $e->getMessage());
            }
        }
        return self::$instance;
    }
}

class Part
{
    private $db;
    private $table = 'master_parts';

    public function __construct()
    {
        $this->db = Database::getConnection();
    }

    public function getAll(): array
    {
        // JOIN menggunakan tabel production_lanes dan mengambil kolom lane_name
        $sql = "SELECT p.*, 
                       c.customer_name, 
                       c.customer_code,
                       COALESCE(l.lane_name, s.subcont_name, NULLIF(p.no_lane, ''), '-') AS no_lane
                FROM {$this->table} p
                LEFT JOIN master_customers c ON p.customer_id = c.id
                LEFT JOIN production_lanes l ON p.source_id = l.id
                LEFT JOIN master_subconts s ON p.source_id = s.id
                ORDER BY p.id DESC";

        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function create(array $data): bool
    {
        $sql = "INSERT INTO {$this->table} 
                (ics_no, part_name, type, category, source_id, no_lane, customer_id, min_stock) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?)";

        $stmt = $this->db->prepare($sql);

        $sourceId = $data['source_id'] ?? $data['lane_id'] ?? null;
        $noLane   = !empty($data['no_lane']) ? trim($data['no_lane']) : null;

        return $stmt->execute([
            !empty($data['ics_no']) ? $data['ics_no'] : null,
            !empty($data['part_name']) ? $data['part_name'] : null,
            !empty($data['type']) ? $data['type'] : null,
            !empty($data['category']) ? $data['category'] : null,
            !empty($sourceId) ? $sourceId : null,
            !empty($noLane) ? $noLane : null,
            !empty($data['customer_id']) ? $data['customer_id'] : null,
            isset($data['min_stock']) && $data['min_stock'] !== '' ? (int)$data['min_stock'] : 0,
        ]);
    }

    public function update(int $id, array $data): bool
    {
        $sql = "UPDATE {$this->table} 
                SET ics_no = ?, 
                    part_name = ?, 
                    type = ?, 
                    category = ?, 
                    source_id = ?, 
                    no_lane = ?,
                    customer_id = ?, 
                    min_stock = ? 
                WHERE id = ?";

        $stmt = $this->db->prepare($sql);

        $sourceId = $data['source_id'] ?? $data['lane_id'] ?? null;
        $noLane   = !empty($data['no_lane']) ? trim($data['no_lane']) : null;

        return $stmt->execute([
            !empty($data['ics_no']) ? $data['ics_no'] : null,
            !empty($data['part_name']) ? $data['part_name'] : null,
            !empty($data['type']) ? $data['type'] : null,
            !empty($data['category']) ? $data['category'] : null,
            !empty($sourceId) ? $sourceId : null,
            !empty($noLane) ? $noLane : null,
            !empty($data['customer_id']) ? $data['customer_id'] : null,
            isset($data['min_stock']) && $data['min_stock'] !== '' ? (int)$data['min_stock'] : 0,
            $id
        ]);
    }

    public function delete(int $id): bool
    {
        $stmt = $this->db->prepare("DELETE FROM {$this->table} WHERE id = ?");
        return $stmt->execute([$id]);
    }
}
