<?php
namespace App\Controllers;

use App\Models\User;

class AuthController {
    
    // Отображение и обработка формы Входа
    public function login() {
        // Если уже вошел — кидаем на главную (пока на группы, например)
        if (isset($_SESSION['user_id'])) {
            header("Location: index.php?route=groups");
            exit;
        }

        $error = '';
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = $_POST['email'] ?? '';
            $password = $_POST['password'] ?? '';

            $user = User::findByEmail($email);

            // Проверяем, есть ли юзер и совпадает ли хеш пароля
            if ($user && password_verify($password, $user['password_hash'])) {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user_name'] = $user['name'];
                $_SESSION['university_id'] = $user['university_id'];

                header("Location: index.php?route=groups");
                exit;
            } else {
                $error = 'Неверный email или пароль!';
            }
        }

        // Подключаем вьюху входа (её сделаем на следующем шаге)
        include dirname(__DIR__) . '/Views/auth/login.view.php';
    }

    // Отображение и обработка формы Регистрации
    public function register() {
        if (isset($_SESSION['user_id'])) {
            header("Location: index.php?route=groups");
            exit;
        }

        $error = '';
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $name = $_POST['name'] ?? '';
            $email = $_POST['email'] ?? '';
            $password = $_POST['password'] ?? '';

            // Проверяем, занят ли email
            if (User::findByEmail($email)) {
                $error = 'Этот email уже зарегистрирован!';
            } else {
                // Если свободен — создаем аккаунт
                if (User::create($name, $email, $password)) {
                    // После успешной регистрации отправляем на вход
                    header("Location: index.php?route=login");
                    exit;
                } else {
                    $error = 'Ошибка базы данных при регистрации.';
                }
            }
        }

        // Подключаем вьюху регистрации
        include dirname(__DIR__) . '/Views/auth/register.view.php';
    }

    // Выход из аккаунта
    public function logout() {
        session_destroy();
        header("Location: index.php?route=login");
        exit;
    }
}