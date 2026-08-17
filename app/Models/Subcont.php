<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Model;

class Subcont extends Model
{
    public function getAll(): array
    {
        $stmt = $this->db->query("
            SELECT * 
            FROM master_subconts 
            ORDER BY id DESC
        ");
        return $stmt->fetchAll();
    }

    public function create(array $data): bool
    {
        $sql = "INSERT INTO master_subconts (subcont_name, category) 
                VALUES (:subcont_name, :category)";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            'subcont_name' => $data['subcont_name'],
            'category'     => $data['category'],
        ]);
    }

    public function update(int $id, array $data): bool
    {
        $sql = "UPDATE master_subconts 
                SET subcont_name = :subcont_name, category = :category 
                WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            'id'           => $id,
            'subcont_name' => $data['subcont_name'],
            'category'     => $data['category'],
        ]);
    }

    public function delete(int $id): bool
    {
        $stmt = $this->db->prepare("DELETE FROM master_subconts WHERE id = :id");
        return $stmt->execute(['id' => $id]);
    }
}
