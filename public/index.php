<?php

ini_set('display_errors', 1);
error_reporting(E_ALL);

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Загрузка конфига
require_once dirname(__DIR__) . '/src/Config/AppConfig.php';
\App\Config\AppConfig::load();

// Инициализация университета
if (!isset($_SESSION['university_id'])) {
    $_SESSION['university_id'] = 1;
}

// Автозагрузка (пока вручную, позже перейдем на Composer)
require_once dirname(__DIR__) . '/src/Config/Database.php';
require_once dirname(__DIR__) . '/src/Models/Room.php';
require_once dirname(__DIR__) . '/src/Controllers/RoomController.php';

$page = $_GET['page'] ?? 'schedule';

// 1. Сначала шапка
require_once dirname(__DIR__) . '/src/Views/layout/header.php';

// 2. Роутинг
switch ($page) {
    case 'classrooms':
        $controller = new \App\Controllers\RoomController();
        $controller->index();
        break;

    case 'schedule':
        echo "<h1>Страница расписания (в разработке)</h1>";
        break;

    default:
        echo "<h1>404 — Страница не найдена</h1>";
        break;
}

// 3. В конце подвал
require_once dirname(__DIR__) . '/src/Views/layout/footer.php';