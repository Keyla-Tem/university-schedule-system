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

    /**
     * Fetches the schedule grid data based on filters.
     * Replaces the old Schedule::getSchedule() method.
     */
    public function getFilteredSchedule(int $semesterId, array $filters = []): array {
        $db = \App\Config\Database::getDB();
        
        $sql = "SELECT se.id, se.day_of_week, se.week_parity, se.status, se.notes,
                       bs.pair_number, bs.start_time, bs.end_time,
                       d.name AS discipline_name,
                       lt.name AS lesson_type_name,
                       CONCAT(t.last_name, ' ', SUBSTRING(t.first_name, 1, 1), '.', SUBSTRING(t.middle_name, 1, 1), '.') AS teacher_short_name,
                       r.room_number,
                       sg.name AS group_name
                FROM schedule_entries se
                JOIN bell_schedules bs ON se.bell_schedule_id = bs.id
                JOIN disciplines d ON se.discipline_id = d.id
                JOIN lesson_types lt ON se.lesson_type_id = lt.id
                JOIN teachers t ON se.teacher_id = t.id
                LEFT JOIN rooms r ON se.room_id = r.id
                JOIN study_groups sg ON se.study_group_id = sg.id
                WHERE se.semester_id = :semester_id AND se.status = 'active'";
        
        $params = [':semester_id' => $semesterId];

        if (!empty($filters['university_id'])) {
            $sql .= " AND se.university_id = :university_id"; // Убедись, что поле university_id есть в таблице se
            $params[':university_id'] = $filters['university_id'];
        }

        if (!empty($filters['study_group_id'])) {
            $sql .= " AND se.study_group_id = :study_group_id";
            $params[':study_group_id'] = $filters['study_group_id'];
        }

        if (!empty($filters['teacher_id'])) {
            $sql .= " AND se.teacher_id = :teacher_id";
            $params[':teacher_id'] = $filters['teacher_id'];
        }

        if (!empty($filters['week_parity']) && $filters['week_parity'] !== 'both') {
            // Include specific parity ('odd' or 'even') PLUS 'both' (every week)
            $sql .= " AND se.week_parity IN (:week_parity, 'both')";
            $params[':week_parity'] = $filters['week_parity'];
        }

        // Sort by day first, then by the pair number (time)
        $sql .= " ORDER BY se.day_of_week, bs.pair_number";

        $stmt = $db->prepare($sql);
        $stmt->execute($params);
        
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }
}