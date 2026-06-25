<?php
namespace App\Controllers;
use App\Models\StudyGroup;

class GroupController {
    public function index() {
        $universityId = $_SESSION['university_id'] ?? 1;

        // Обработка действий
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (isset($_POST['action']) && $_POST['action'] === 'create_group') {
                StudyGroup::create($universityId, (int)$_POST['organization_unit_id'], $_POST['name'], (int)$_POST['student_count']);
                header("Location: /keyschedule/public/index.php?route=groups");
                exit;
            }
            if (isset($_POST['action']) && $_POST['action'] === 'delete_group') {
                StudyGroup::delete((int)$_POST['group_id']);
                header("Location: /keyschedule/public/index.php?route=groups");
                exit;
            }
        }

        $groups = StudyGroup::getAll();
        
        // Кафедры для выбора
        $db = \App\Config\Database::getDB();
        $departments = $db->prepare("SELECT id, name FROM organization_units WHERE university_id = ?");
        $departments->execute([$universityId]);
        $units = $departments->fetchAll(\PDO::FETCH_ASSOC);

        include dirname(__DIR__) . '/Views/units/groups.view.php';
    }
}