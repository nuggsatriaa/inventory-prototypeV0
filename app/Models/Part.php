<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Model;

class Part extends Model
{
    public function getAll(): array
    {
        $stmt = $this->db->query("
            SELECT p.*, c.customer_name 
            FROM master_parts p 
            LEFT JOIN master_customers c ON p.customer_id = c.id 
            ORDER BY p.id DESC
        ");
        return $stmt->fetchAll();
    }

    public function create(array $data): bool
    {
        $sql = "INSERT INTO master_parts (part_number, part_name, type, source, group_code, customer_id, min_stock) 
                VALUES (:part_number, :part_name, :type, :source, :group_code, :customer_id, :min_stock)";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            'part_number' => $data['part_number'],
            'part_name'   => $data['part_name'],
            'type'        => $data['type'],
            'source'      => $data['source'] ?? null,
            'group_code'  => $data['group_code'],
            'customer_id' => !empty($data['customer_id']) ? $data['customer_id'] : null,
            'min_stock'   => $data['min_stock'] ?? 0,
        ]);
    }

    public function update(int $id, array $data): bool
    {
        $sql = "UPDATE master_parts 
                SET part_number = :part_number, part_name = :part_name, type = :type, 
                    source = :source, group_code = :group_code, customer_id = :customer_id, min_stock = :min_stock 
                WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            'id'          => $id,
            'part_number' => $data['part_number'],
            'part_name'   => $data['part_name'],
            'type'        => $data['type'],
            'source'      => $data['source'] ?? null,
            'group_code'  => $data['group_code'],
            'customer_id' => !empty($data['customer_id']) ? $data['customer_id'] : null,
            'min_stock'   => $data['min_stock'] ?? 0,
        ]);
    }

    public function delete(int $id): bool
    {
        $stmt = $this->db->prepare("DELETE FROM master_parts WHERE id = :id");
        return $stmt->execute(['id' => $id]);
    }
}
