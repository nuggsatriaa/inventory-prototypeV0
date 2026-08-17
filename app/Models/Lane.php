<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Model;

class Lane extends Model
{
    public function getAll(): array
    {
        $stmt = $this->db->query("
            SELECT * 
            FROM production_lanes 
            ORDER BY id DESC
        ");
        return $stmt->fetchAll();
    }

    public function getAllGroupedByCategory(): array
    {
        $stmt = $this->db->query("
            SELECT * 
            FROM production_lanes 
            ORDER BY category_name ASC, lane_name ASC
        ");
        $results = $stmt->fetchAll();

        $grouped = [];
        foreach ($results as $row) {
            $category = $row['category_name'] ?? 'Other';
            $grouped[$category][] = $row;
        }

        return $grouped;
    }

    public function create(array $data): bool
    {
        $sql = "INSERT INTO production_lanes (group_name, category_name, lane_name) 
                VALUES (:group_name, :category_name, :lane_name)";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            'group_name'    => $data['group_name'],
            'category_name' => $data['category_name'],
            'lane_name'     => $data['lane_name'],
        ]);
    }

    public function update(int $id, array $data): bool
    {
        $sql = "UPDATE production_lanes 
                SET group_name = :group_name, category_name = :category_name, lane_name = :lane_name 
                WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            'id'            => $id,
            'group_name'    => $data['group_name'],
            'category_name' => $data['category_name'],
            'lane_name'     => $data['lane_name'],
        ]);
    }

    public function delete(int $id): bool
    {
        $stmt = $this->db->prepare("DELETE FROM production_lanes WHERE id = :id");
        return $stmt->execute(['id' => $id]);
    }
}
