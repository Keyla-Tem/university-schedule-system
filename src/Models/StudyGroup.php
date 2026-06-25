<?php
namespace App\Models;

class StudyGroup extends BaseModel {
    
    public static function getAll() {
        $db = \App\Config\Database::getDB();
    
        $stmt = $db->query("
            SELECT * FROM study_groups 
            ORDER BY name ASC
        ");
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    // Мы оставляем $unitId в аргументах, чтобы GroupController не выдал ошибку,
    // но в сам SQL-запрос передаем только существующие колонки.
    public static function create($universityId, $unitId, $name, $studentCount) {
        // Небольшая защита: если имя пустое, даже не пытаемся писать в базу
        if (empty(trim($name))) {
            return false;
        }

        $db = \App\Config\Database::getDB();
        $stmt = $db->prepare("
            INSERT INTO study_groups (university_id, name, student_count, is_active)
            VALUES (?, ?, ?, 1)
        ");
        return $stmt->execute([
            $universityId, 
            trim($name), 
            (int)$studentCount
        ]);
    }

    public static function delete($id) {
        $db = \App\Config\Database::getDB();
        $stmt = $db->prepare("DELETE FROM study_groups WHERE id = ?");
        return $stmt->execute([$id]);
    }
}