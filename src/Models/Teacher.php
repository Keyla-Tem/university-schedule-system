<?php
namespace App\Models;

class Teacher extends BaseModel {
    public static function getAll() {
        $db = \App\Config\Database::getDB();
        $stmt = $db->query("SELECT * FROM teachers WHERE is_active = 1 ORDER BY last_name ASC");
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }
}