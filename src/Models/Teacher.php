<?php
namespace App\Models;

class Teacher extends BaseModel {
    
    /**
     * Получаем всех активных преподавателей с названиями их кафедр
     */
    public static function getAll() {
        $db = \App\Config\Database::getDB();
        $stmt = $db->query("
            SELECT t.*, ou.name AS department_name 
            FROM teachers t
            LEFT JOIN organization_units ou ON t.organization_unit_id = ou.id
            WHERE t.is_active = 1 
            ORDER BY t.last_name ASC, t.first_name ASC
        ");
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    /**
     * Добавление нового преподавателя
     */
    public static function create($universityId, $unitId, $lastName, $firstName, $middleName, $degree, $position, $email) {
        $db = \App\Config\Database::getDB();
        $stmt = $db->prepare("
            INSERT INTO teachers (university_id, organization_unit_id, last_name, first_name, middle_name, degree, position, email, max_hours_per_day, max_pairs_per_day, is_active)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, 8, 4, 1)
        ");
        return $stmt->execute([
            $universityId,
            $unitId,
            trim($lastName),
            trim($firstName),
            trim($middleName),
            trim($degree),
            trim($position),
            trim($email)
        ]);
    }

    /**
     * "Мягкое" или полное удаление. В ТЗ у тебя есть флаг is_active. 
     * Давай делать полное удаление, как и в аудиториях, но если привязан к расписанию, 
     * база сама не даст удалить из-за ограничений ключей (и это правильно).
     */
    public static function delete($id) {
        $db = \App\Config\Database::getDB();
        $stmt = $db->prepare("DELETE FROM teachers WHERE id = ?");
        return $stmt->execute([$id]);
    }
}