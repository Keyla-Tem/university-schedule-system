<?php
namespace App\Controllers;

use App\Models\User;
use App\Models\University;
use App\Models\StudyGroup;

class SettingsController {
    public function index() {
        $userId = $_SESSION['user_id'];
        $user = \App\Models\User::findById($userId);
        
        // Вместо вызова класса напрямую, создаем объект:
        $universityModel = new \App\Models\University();
        $groupModel = new \App\Models\StudyGroup();
        
        $universities = $universityModel->getAll();
        $groups = $groupModel->getAll();
        
        require_once dirname(__DIR__) . '/Views/settings/index.view.php';
    }

    public function update() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $userId = $_SESSION['user_id'];
            $name = $_POST['name'];
            $email = $_POST['email'];
            $universityId = (int)$_POST['university_id'];
            $groupId = (int)$_POST['group_id'];
            $password = $_POST['password'] ?? null;

            User::updateProfile($userId, $name, $email, $universityId, $groupId, $password);
            
            // Обновляем сессию, чтобы имя в сайдбаре поменялось сразу
            $_SESSION['user_name'] = $name;
            
            header("Location: index.php?route=settings&status=success");
        }
    }
}