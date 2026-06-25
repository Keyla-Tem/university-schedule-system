<?php
namespace App\Controllers;

use App\Models\User;

class AuthController {
    
    // Отображение и обработка формы Входа
    public function login() {
        if (isset($_SESSION['user_id'])) {
            header("Location: index.php?route=schedule");
            exit;
        }

        $error = '';
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = $_POST['email'] ?? '';
            $password = $_POST['password'] ?? '';

            $user = User::findByEmail($email);

            if ($user && password_verify($password, $user['password_hash'])) {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user_name'] = $user['name'];
                $_SESSION['university_id'] = $user['university_id'];
                
                // Сохраняем роль пользователя для разделения прав
                $_SESSION['role'] = $user['role'] ?? 'user'; 

                header("Location: index.php?route=schedule");
                exit;
            } else {
                $error = 'Неверный email или пароль!';
            }
        }

        include dirname(__DIR__) . '/Views/auth/login.view.php';
    }

    // Отображение и обработка формы Регистрации
    public function register() {
        if (isset($_SESSION['user_id'])) {
            header("Location: index.php?route=schedule");
            exit;
        }

        $error = '';
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $name = $_POST['name'] ?? '';
            $email = $_POST['email'] ?? '';
            $password = $_POST['password'] ?? '';

            if (User::findByEmail($email)) {
                $error = 'Этот email уже зарегистрирован!';
            } else {
                if (User::create($name, $email, $password)) {
                    header("Location: index.php?route=login");
                    exit;
                } else {
                    $error = 'Ошибка базы данных при регистрации.';
                }
            }
        }

        include dirname(__DIR__) . '/Views/auth/register.view.php';
    }

    // Выход из аккаунта
    public function logout() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        $_SESSION = [];
        session_destroy();
        header("Location: index.php?route=login");
        exit;
    }
}