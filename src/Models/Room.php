<?php

namespace App\Models;

use App\Config\Database;
use PDO;

class Room
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getInstance()->getConnection();
    }

    public function getAllByUniversity(int $universityId): array
    {
        $stmt = $this->db->prepare("
            SELECT 
                r.id,
                r.room_number,
                r.capacity,
                rt.name AS type,
                b.name AS building_name,
                COUNT(se.id) AS lesson_count
            FROM rooms r
            LEFT JOIN buildings b ON r.building_id = b.id
            LEFT JOIN room_types rt ON r.room_type_id = rt.id
            LEFT JOIN schedule_entries se ON se.room_id = r.id
                AND se.semester_id = (SELECT id FROM semesters WHERE is_active = 1 AND university_id = ? LIMIT 1)
            WHERE b.university_id = ? OR r.id IN (SELECT room_id FROM schedule_entries WHERE university_id = ?)
            GROUP BY r.id, r.room_number, r.capacity, rt.name, b.name
            ORDER BY b.name, r.room_number
        ");
        
        // Передаем переменную 3 раза — по разу на каждый знак "?" в запросе
        $stmt->execute([$universityId, $universityId, $universityId]);
        return $stmt->fetchAll();
    }

    public function getById(int $roomId): ?array
    {
        $stmt = $this->db->prepare("
            SELECT r.room_number, r.capacity, rt.name AS type, b.name AS building_name
            FROM rooms r 
            LEFT JOIN buildings b ON r.building_id = b.id
            LEFT JOIN room_types rt ON r.room_type_id = rt.id
            WHERE r.id = ?
        ");
        $stmt->execute([$roomId]);
        return $stmt->fetch() ?: null;
    }

    public function getSchedule(int $roomId, int $universityId): array
    {
        $stmt = $this->db->prepare("
            SELECT
                se.day_of_week,
                bs.pair_number,
                se.week_parity,
                d.name AS discipline_name,
                lt.name AS lesson_type,
                CONCAT(t.last_name, ' ', LEFT(t.first_name, 1), '.', LEFT(t.middle_name, 1), '.') AS teacher_name,
                sg.name AS group_name,
                bs.start_time,
                bs.end_time
            FROM schedule_entries se
            LEFT JOIN study_groups sg ON se.study_group_id = sg.id
            LEFT JOIN disciplines d ON se.discipline_id = d.id
            LEFT JOIN lesson_types lt ON se.lesson_type_id = lt.id
            LEFT JOIN teachers t ON se.teacher_id = t.id
            -- Связываем через bell_schedule_id, как заложено в структуре БД
            LEFT JOIN bell_schedules bs ON se.bell_schedule_id = bs.id
            WHERE se.room_id = ? 
              AND se.semester_id = (SELECT id FROM semesters WHERE is_active = 1 AND university_id = ? LIMIT 1)
            ORDER BY se.day_of_week, bs.pair_number
        ");
        
        $stmt->execute([$roomId, $universityId]);
        return $stmt->fetchAll();
    }
}