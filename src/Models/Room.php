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
                r.type,
                b.name AS building_name,
                COUNT(se.id) AS lesson_count
            FROM rooms r
            LEFT JOIN buildings b ON r.building_id = b.id
            LEFT JOIN schedule_entries se ON se.room_id = r.id
                AND se.semester_id = (SELECT id FROM semesters WHERE is_active = 1 AND university_id = :uni_id LIMIT 1)
            WHERE b.university_id = :uni_id OR r.id IN (SELECT room_id FROM schedule_entries WHERE university_id = :uni_id)
            GROUP BY r.id
            ORDER BY b.name, r.room_number
        ");
        
        $stmt->execute(['uni_id' => $universityId]);
        return $stmt->fetchAll();
    }

    public function getById(int $roomId): ?array
    {
        $stmt = $this->db->prepare("
            SELECT r.room_number, r.capacity, r.type, b.name AS building_name
            FROM rooms r 
            LEFT JOIN buildings b ON r.building_id = b.id
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
                se.pair_number,
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
            LEFT JOIN bell_schedules bs ON se.pair_number = bs.pair_number AND bs.university_id = :uni_id
            WHERE se.room_id = :room_id 
              AND se.semester_id = (SELECT id FROM semesters WHERE is_active = 1 AND university_id = :uni_id LIMIT 1)
            ORDER BY se.day_of_week, se.pair_number
        ");
        
        $stmt->execute([
            'room_id' => $roomId,
            'uni_id' => $universityId
        ]);
        return $stmt->fetchAll();
    }
}