<?php
// functions.php - Вспомогательные функции

/**
 * Получение данных расписания из БД
 */
function getScheduleData($pdo, $university_id = 1) {
    $sql = "
        SELECT 
            se.id,
            se.day_of_week,
            se.pair_number,
            se.week_parity,
            se.status,
            se.notes,
            sg.name AS group_name,
            d.name AS discipline_name,
            d.code AS discipline_code,
            lt.name AS lesson_type,
            CONCAT(t.last_name, ' ', LEFT(t.first_name, 1), '.', LEFT(t.middle_name, 1), '.') AS teacher_name,
            r.room_number AS room,
            b.name AS building,
            bs.start_time,
            bs.end_time
        FROM schedule_entries se
        LEFT JOIN study_groups sg ON se.study_group_id = sg.id
        LEFT JOIN disciplines d ON se.discipline_id = d.id
        LEFT JOIN lesson_types lt ON se.lesson_type_id = lt.id
        LEFT JOIN teachers t ON se.teacher_id = t.id
        LEFT JOIN rooms r ON se.room_id = r.id
        LEFT JOIN buildings b ON r.building_id = b.id
        LEFT JOIN bell_schedules bs ON se.pair_number = bs.pair_number AND bs.university_id = se.university_id
        WHERE se.semester_id = (SELECT id FROM semesters WHERE is_active = 1 AND university_id = ? LIMIT 1)
        ORDER BY se.day_of_week, se.pair_number
    ";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$university_id]);
    return $stmt->fetchAll();
}

/**
 * Группировка расписания по дням и парам
 */
function groupSchedule($schedule) {
    $grid = [];
    foreach ($schedule as $row) {
        $day = $row['day_of_week'];
        $pair = $row['pair_number'];
        $grid[$day][$pair][] = $row;
    }
    return $grid;
}

/**
 * Получение статистики
 */
function getStats($pdo, $university_id = 1) {
    $stats = [];
    
    // Количество занятий
    $stmt = $pdo->prepare("
        SELECT COUNT(*) as total 
        FROM schedule_entries se
        WHERE se.semester_id = (SELECT id FROM semesters WHERE is_active = 1 AND university_id = ? LIMIT 1)
    ");
    $stmt->execute([$university_id]);
    $stats['lessons'] = $stmt->fetch()['total'] ?? 0;
    
    // Количество групп
    $stmt = $pdo->prepare("SELECT COUNT(*) as total FROM study_groups WHERE university_id = ? AND is_active = 1");
    $stmt->execute([$university_id]);
    $stats['groups'] = $stmt->fetch()['total'] ?? 0;
    
    // Количество преподавателей
    $stmt = $pdo->prepare("SELECT COUNT(*) as total FROM teachers WHERE university_id = ? AND is_active = 1");
    $stmt->execute([$university_id]);
    $stats['teachers'] = $stmt->fetch()['total'] ?? 0;
    
    // Название активного семестра
    $stmt = $pdo->prepare("SELECT name FROM semesters WHERE is_active = 1 AND university_id = ? LIMIT 1");
    $stmt->execute([$university_id]);
    $stats['semester'] = $stmt->fetchColumn() ?: 'Семестр не выбран';
    
    return $stats;
}

/**
 * Получение времени для пары
 */
function getPairTime($schedule, $pair_number) {
    foreach ($schedule as $row) {
        if ($row['pair_number'] == $pair_number && isset($row['start_time'])) {
            return date('H:i', strtotime($row['start_time'])) . '–' . date('H:i', strtotime($row['end_time']));
        }
    }
    return '—';
}

/**
 * Безопасное экранирование
 */
function e($str) {
    return htmlspecialchars($str ?? '', ENT_QUOTES, 'UTF-8');
}

/**
 * Статус с цветом
 */
function getStatusClass($status) {
    $map = [
        'planned' => 'status-planned',
        'confirmed' => 'status-confirmed',
        'cancelled' => 'status-cancelled',
        'rescheduled' => 'status-rescheduled'
    ];
    return $map[$status] ?? 'status-planned';
}

/**
 * Иконка для статуса
 */
function getStatusIcon($status) {
    $map = [
        'planned' => '⏳',
        'confirmed' => '✓',
        'cancelled' => '✕',
        'rescheduled' => '⟳'
    ];
    return $map[$status] ?? '';
}
?>