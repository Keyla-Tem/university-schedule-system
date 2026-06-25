<?php
namespace App\Controllers;

class TeacherController {
    public function index() {
        // 1. Получаем ID университета из сессии
        $universityId = $_SESSION['university_id'] ?? 1;

        // ============================================================
        // ОБРАБОТКА ПОСТ-ЗАПРОСОВ (CRUD ЛОГИКА)
        // ============================================================
        
        // Добавление преподавателя
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'create_teacher') {
            $unitId = (int)($_POST['organization_unit_id'] ?? 0);
            $lastName = trim($_POST['last_name'] ?? '');
            $firstName = trim($_POST['first_name'] ?? '');
            $middleName = trim($_POST['middle_name'] ?? '');
            $degree = trim($_POST['degree'] ?? 'Нет');
            $position = trim($_POST['position'] ?? 'Преподаватель');
            $email = trim($_POST['email'] ?? '');

            if (!empty($lastName) && !empty($firstName) && $unitId > 0) {
                \App\Models\Teacher::create($universityId, $unitId, $lastName, $firstName, $middleName, $degree, $position, $email);
                
                header("Location: /keyschedule/public/index.php?route=teachers");
                exit;
            }
        }

        // Удаление преподавателя
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'delete_teacher') {
            $teacherId = (int)($_POST['teacher_id'] ?? 0);
            
            if ($teacherId > 0) {
                \App\Models\Teacher::delete($teacherId);
                
                header("Location: /keyschedule/public/index.php?route=teachers");
                exit;
            }
        }

        // ============================================================
        // СБОР ДАННЫХ ДЛЯ ВЬЮХИ
        // ============================================================
        
        $teachers = \App\Models\Teacher::getAll(); // Получаем данные преподов

        // Получаем кафедры/подразделения текущего вуза для селекта в форме
        $db = \App\Config\Database::getDB();
        $stmtUnits = $db->prepare("SELECT id, name FROM organization_units WHERE university_id = ? AND unit_type = 'department' ORDER BY name");
        $stmtUnits->execute([$universityId]);
        $departments = $stmtUnits->fetchAll(\PDO::FETCH_ASSOC);

        // Передаем всё в представление
        include dirname(__DIR__) . '/Views/units/teachers.view.php';
    }
}