<?php

namespace App\Models;

use App\Core\Model; // Sesuaikan jika ada base Model di App\Core
use PDO;

class Customer extends Model
{
    protected $table = 'master_customers';

    public function getAll(): array
    {
        $stmt = $this->db->prepare("SELECT * FROM {$this->table} ORDER BY id DESC");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getById(int $id)
    {
        $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE id = :id LIMIT 1");
        $stmt->execute([':id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function create(array $data): bool
    {
        $stmt = $this->db->prepare("INSERT INTO {$this->table} (customer_name, customer_code) VALUES (:customer_name, :customer_code)");
        return $stmt->execute([
            ':customer_name' => $data['customer_name'],
            ':customer_code' => $data['customer_code']
        ]);
    }

    public function update(int $id, array $data): bool
    {
        $stmt = $this->db->prepare("UPDATE {$this->table} SET customer_name = :customer_name, customer_code = :customer_code WHERE id = :id");
        return $stmt->execute([
            ':id'           => $id,
            ':customer_name' => $data['customer_name'],
            ':customer_code' => $data['customer_code']
        ]);
    }

    public function delete(int $id): bool
    {
        $stmt = $this->db->prepare("DELETE FROM {$this->table} WHERE id = :id");
        return $stmt->execute([':id' => $id]);
    }
}
