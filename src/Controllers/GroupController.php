<?php
namespace App\Controllers;

use App\Models\StudyGroup;

class GroupController {
    public function index() {
        // Проверка: если юзер вообще не залогинен — выкидываем на форму входа
        if (!isset($_SESSION['user_id'])) {
            header("Location: index.php?route=login");
            exit;
        }

        $universityId = $_SESSION['university_id'] ?? 1;

        // Обработка действий (Создание и Удаление)
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            
            // КРИТИЧЕСКИЙ ДЕФЕНС: Если роль не админ — рубим запрос сразу!
            if (($_SESSION['role'] ?? 'user') !== 'admin') {
                header("HTTP/1.1 403 Forbidden");
                echo "<h1>403 Forbidden — У вас нет прав на редактирование данных!</h1>";
                exit;
            }

            // Если прорвался админ — выполняем его команды
            if (isset($_POST['action']) && $_POST['action'] === 'create_group') {
                StudyGroup::create($universityId, (int)$_POST['organization_unit_id'], $_POST['name'], (int)$_POST['student_count']);
                header("Location: index.php?route=groups");
                exit;
            }
            if (isset($_POST['action']) && $_POST['action'] === 'delete_group') {
                StudyGroup::delete((int)$_POST['group_id']);
                header("Location: index.php?route=groups");
                exit;
            }
        }

        // Этот блок (просмотр списка групп) доступен и Админу, и Студенту
        $groups = StudyGroup::getAll();
        
        // Кафедры для выпадающего списка
        $db = \App\Config\Database::getDB();
        $departments = $db->prepare("SELECT id, name FROM organization_units WHERE university_id = ?");
        $departments->execute([$universityId]);
        $units = $departments->fetchAll(\PDO::FETCH_ASSOC);

        include dirname(__DIR__) . '/Views/units/groups.view.php';
    }
}