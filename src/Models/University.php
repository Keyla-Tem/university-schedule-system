<?php

namespace App\Models;

class University extends BaseModel
{
    public function getAll(): array
    {
        $stmt = $this->db->query("SELECT * FROM universities ORDER BY name ASC");
        return $stmt->fetchAll();
    }

    public function getById(int $id): ?array
    {
        $stmt = $this->db->prepare("SELECT * FROM universities WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch() ?: null;
    }

    public function create(string $name, ?string $shortName): bool
    {
        $stmt = $this->db->prepare("INSERT INTO universities (name, short_name) VALUES (?, ?)");
        return $stmt->execute([$name, $shortName]);
    }

    public function update(int $id, string $name, ?string $shortName): bool
    {
        $stmt = $this->db->prepare("UPDATE universities SET name = ?, short_name = ? WHERE id = ?");
        return $stmt->execute([$name, $shortName, $id]);
    }

    public function delete(int $id): bool
    {
        $stmt = $this->db->prepare("DELETE FROM universities WHERE id = ?");
        return $stmt->execute([$id]);
    }
}