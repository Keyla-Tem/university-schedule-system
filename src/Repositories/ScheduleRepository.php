<?php
namespace App\Repositories;
use App\Config\Database;

class ScheduleRepository {
    public function getTeacherDailyLoad($teacherId, $dayOfWeek, $semesterId) {
        $db = Database::getDB();
        // Считаем количество записей и общую длительность в часах (с учетом bell_schedules)
        $stmt = $db->prepare("
            SELECT COUNT(se.id) as count, 
                   SUM(TIMESTAMPDIFF(SECOND, bs.start_time, bs.end_time)) / 3600 as total_hours
            FROM schedule_entries se
            JOIN bell_schedules bs ON se.bell_schedule_id = bs.id
            WHERE se.teacher_id = :teacher_id 
              AND se.day_of_week = :day_of_week 
              AND se.semester_id = :semester_id
        ");
        $stmt->execute([
            'teacher_id' => $teacherId,
            'day_of_week' => $dayOfWeek,
            'semester_id' => $semesterId
        ]);
        return $stmt->fetch(\PDO::FETCH_ASSOC);
    }

    public function hasGroupConflict($semesterId, $dayOfWeek, $bellScheduleId, $studyGroupId, $excludeId = null) {
        $db = Database::getDB();
        
        $sql = "SELECT COUNT(*) 
                FROM schedule_entries 
                WHERE semester_id = ? 
                AND day_of_week = ? 
                AND bell_schedule_id = ? 
                AND study_group_id = ?";
        
        $params = [$semesterId, $dayOfWeek, $bellScheduleId, $studyGroupId];
        
        // Если мы редактируем существующую запись, нам нужно игнорировать её саму
        if ($excludeId !== null) {
            $sql .= " AND id != ?";
            $params[] = $excludeId;
        }
        
        $stmt = $db->prepare($sql);
        $stmt->execute($params);
        
        return (int)$stmt->fetchColumn() > 0;
    }
}