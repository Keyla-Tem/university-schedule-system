<?php

namespace App\Models;

use App\Config\Database;
use PDO;

class Timetable
{
    public static function getByGroup(int $universityId, int $groupId): array
    {
        $db = Database::getDB();

        $stmt = $db->prepare("
            SELECT
                se.id,
                se.day_of_week,
                se.week_parity,

                bs.pair_number,
                bs.start_time,
                bs.end_time,

                d.name AS discipline_name,

                lt.name AS lesson_type,

                CONCAT(
                    t.last_name,
                    ' ',
                    t.first_name,
                    IF(
                        t.middle_name IS NULL OR t.middle_name = '',
                        '',
                        CONCAT(' ', t.middle_name)
                    )
                ) AS teacher_name,

                r.room_number

            FROM schedule_entries se

            INNER JOIN disciplines d
                ON d.id = se.discipline_id

            INNER JOIN teachers t
                ON t.id = se.teacher_id

            INNER JOIN rooms r
                ON r.id = se.room_id

            INNER JOIN bell_schedules bs
                ON bs.id = se.bell_schedule_id

            INNER JOIN lesson_types lt
                ON lt.id = se.lesson_type_id

            WHERE
                se.university_id = ?
                AND se.study_group_id = ?
                AND se.status = 'active'

            ORDER BY
                se.day_of_week,
                bs.pair_number
        ");

        $stmt->execute([
            $universityId,
            $groupId
        ]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}