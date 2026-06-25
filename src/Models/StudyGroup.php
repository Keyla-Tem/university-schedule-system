<?php
namespace App\Models;

class StudyGroup extends BaseModel {
    public static function getAll() {
        $db = \App\Config\Database::getDB();
        $stmt = $db->query("SELECT * FROM study_groups ORDER BY name ASC");
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }
}