<?php

if (!defined('BASE_URL')) {
    $scriptName = $_SERVER['SCRIPT_NAME'];
    $baseUrl = str_replace('/index.php', '', $scriptName);
    define('BASE_URL', $baseUrl);
}

ini_set('display_errors', 1);
error_reporting(E_ALL);

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Загрузка конфигурации
require_once dirname(__DIR__) . '/src/Config/AppConfig.php';
\App\Config\AppConfig::load();

if (!isset($_SESSION['university_id'])) {
    $_SESSION['university_id'] = 1;
}

// Подключение моделей данных
require_once dirname(__DIR__) . '/src/Config/Database.php';
require_once dirname(__DIR__) . '/src/Models/BaseModel.php';
require_once dirname(__DIR__) . '/src/Models/Room.php';
require_once dirname(__DIR__) . '/src/Models/University.php';
require_once dirname(__DIR__) . '/src/Models/OrganizationUnit.php';
require_once dirname(__DIR__) . '/src/Models/StudyGroup.php';
require_once dirname(__DIR__) . '/src/Models/Teacher.php';
require_once dirname(__DIR__) . '/src/Models/Schedule.php';
require_once dirname(__DIR__) . '/src/Models/User.php';
require_once dirname(__DIR__) . '/src/Models/Timetable.php';
// Подключение контроллеров
require_once dirname(__DIR__) . '/src/Controllers/BaseController.php';
require_once dirname(__DIR__) . '/src/Controllers/SettingsController.php';
require_once dirname(__DIR__) . '/src/Controllers/RoomController.php';
require_once dirname(__DIR__) . '/src/Controllers/UniversityController.php';
require_once dirname(__DIR__) . '/src/Controllers/OrganizationUnitController.php';
require_once dirname(__DIR__) . '/src/Controllers/GroupController.php';
require_once dirname(__DIR__) . '/src/Controllers/TeacherController.php';
require_once dirname(__DIR__) . '/src/Controllers/ScheduleController.php';
require_once dirname(__DIR__) . '/src/Controllers/TimetableController.php';
require_once dirname(__DIR__) . '/src/Controllers/DashboardController.php';
require_once dirname(__DIR__) . '/src/Controllers/AuthController.php';

$route = $_GET['route'] ?? 'timetable'; 
$action = $_GET['action'] ?? 'index';

// === БЛОК ЗАЩИТЫ (AUTH GUARD) ===
if (!isset($_SESSION['user_id']) && $route !== 'login' && $route !== 'register') {
    header("Location: index.php?route=login");
    exit;
}
// =================================

ob_start();

switch ($route) {
    case 'login':
        $controller = new \App\Controllers\AuthController();
        $controller->login();
        exit;

    case 'register':
        $controller = new \App\Controllers\AuthController();
        $controller->register();
        exit;

    case 'logout':
        $controller = new \App\Controllers\AuthController();
        $controller->logout();
        exit;

    case 'dashboard':
        $controller = new \App\Controllers\DashboardController();
        $controller->index();
        break;

    case 'reports':
        $controller = new \App\Controllers\DashboardController();
        $controller->index();
        break;

    case 'timetable':
        $controller = new \App\Controllers\TimetableController();
        $controller->index();
        break;

    case 'schedule':
        $controller = new \App\Controllers\ScheduleController();
        $controller->index();
        break;

    case 'classrooms':
        $controller = new \App\Controllers\RoomController();
        $controller->index();
        break;

    case 'universities':
        $controller = new \App\Controllers\UniversityController();
        if (method_exists($controller, $action)) {
            $controller->$action();
        }
        break;

    case 'units':
        $controller = new \App\Controllers\OrganizationUnitController();
        if (method_exists($controller, $action)) {
            $controller->$action();
        }
        break;

    case 'groups':
        $controller = new \App\Controllers\GroupController();
        $controller->index();
        break;

    case 'teachers':
        $controller = new \App\Controllers\TeacherController();
        $controller->index();
        break;

    case 'settings':
        $controller = new \App\Controllers\SettingsController();
        if ($action === 'update') {
            $controller->update();
        } else {
            $controller->index();
        }
        break;

    default:
        header("HTTP/1.0 404 Not Found");
        echo "<h1>404 — Страница не найдена</h1>";
        break;
}

$content = ob_get_clean();

require_once dirname(__DIR__) . '/src/Views/layout/header.php';
echo $content;
require_once dirname(__DIR__) . '/src/Views/layout/footer.php';