<?php
namespace App\Models;

use App\Config\Database;

class Schedule extends BaseModel {

    public static function getSchedule() {
        $db = Database::getDB();
        
        $sql = "SELECT se.*, bs.start_time, d.name AS subject_name, 
                       CONCAT(t.last_name, ' ', t.first_name) AS teacher_name
                FROM schedule_entries se
                JOIN bell_schedules bs ON se.bell_schedule_id = bs.id
                JOIN disciplines d ON se.discipline_id = d.id
                JOIN teachers t ON se.teacher_id = t.id
                ORDER BY se.day_of_week, bs.start_time";
                
        $stmt = $db->query($sql);
        return $stmt->fetchAll();
    }
}