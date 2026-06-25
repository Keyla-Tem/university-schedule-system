<?php
namespace App\Controllers;

use App\Config\Database;

class DashboardController {
    public function index() {
        $db = Database::getDB();

        // Статистика (общие цифры)
        $stats = [
            'users'    => $db->query("SELECT COUNT(*) FROM users")->fetchColumn(),
            'teachers' => $db->query("SELECT COUNT(*) FROM teachers")->fetchColumn(),
            'groups'   => $db->query("SELECT COUNT(*) FROM study_groups")->fetchColumn(),
            'lessons'  => $db->query("SELECT COUNT(*) FROM schedule_entries")->fetchColumn()
        ];

        // Топ-группы по количеству пар
        $topGroups = $db->query("
            SELECT g.name, COUNT(s.id) as count 
            FROM study_groups g 
            JOIN schedule_entries s ON g.id = s.study_group_id 
            GROUP BY g.id ORDER BY count DESC LIMIT 3
        ")->fetchAll(\PDO::FETCH_ASSOC);

        // Последние 5 пользователей
        $recentUsers = $db->query("SELECT name, email, created_at FROM users ORDER BY created_at DESC LIMIT 5")->fetchAll(\PDO::FETCH_ASSOC);

        include dirname(__DIR__) . '/Views/dashboard.view.php';
    }
}