<?php

namespace App\Models;

class OrganizationUnit extends BaseModel
{
    
    // Извлекает юниты вместе с их родительскими именами для получения четкого списка
    
    public function getAllByUniversity(int $universityId): array
    {
        $sql = "SELECT ou.*, p.name as parent_name 
                FROM organization_units ou
                LEFT JOIN organization_units p ON ou.parent_id = p.id
                WHERE ou.university_id = ?
                ORDER BY ou.parent_id ASC, ou.name ASC";
                
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$universityId]);
        return $stmt->fetchAll();
    }

    public function getById(int $id): ?array
    {
        $stmt = $this->db->prepare("SELECT * FROM organization_units WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch() ?: null;
    }

    // Обеспечивает иерархию: (факультет, кафедра, институт, другое)

    public function create(int $universityId, ?int $parentId, string $name, ?string $shortName, string $unitType): bool
    {
        $sql = "INSERT INTO organization_units (university_id, parent_id, name, short_name, unit_type) 
                VALUES (?, ?, ?, ?, ?)";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$universityId, $parentId, $name, $shortName, $unitType]);
    }

    public function update(int $id, ?int $parentId, string $name, ?string $shortName, string $unitType): bool
    {
        // Техническая защита: организация не может быть своей собственной материнской компанией
        if ($parentId === $id) {
            $parentId = null;
        }

        $sql = "UPDATE organization_units 
                SET parent_id = ?, name = ?, short_name = ?, unit_type = ? 
                WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$parentId, $name, $shortName, $unitType, $id]);
    }

    public function delete(int $id): bool
    {
        $stmt = $this->db->prepare("DELETE FROM organization_units WHERE id = ?");
        return $stmt->execute([$id]);
    }
}