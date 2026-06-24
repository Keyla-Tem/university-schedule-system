<?php

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

// Подключение контроллеров
require_once dirname(__DIR__) . '/src/Controllers/RoomController.php';
require_once dirname(__DIR__) . '/src/Controllers/UniversityController.php';
require_once dirname(__DIR__) . '/src/Controllers/OrganizationUnitController.php';

// Слой роутинга: route выбирает раздел, action — действие (метод контроллера)
$route = $_GET['route'] ?? 'schedule';
$action = $_GET['action'] ?? 'index';

ob_start();

switch ($route) {
    case 'classrooms':
        $controller = new \App\Controllers\RoomController();
        $controller->index();
        break;

    case 'universities':
        $controller = new \App\Controllers\UniversityController();
        // Динамически вызываем метод, если он существует в контроллере
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

    case 'schedule':
        echo "<h1>Страница расписания (в разработке)</h1>";
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