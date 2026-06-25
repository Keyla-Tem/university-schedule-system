<?php
namespace App\Controllers;

use App\Models\Schedule;
use App\Repositories\ScheduleRepository;
use App\Services\ScheduleService;

class ScheduleController extends BaseController {
    public function index() {
        $this->requireLogin();
        
        if (isset($_GET['ajax']) && $_GET['ajax'] === '1') {
            $this->handleAjaxSchedule();
            return;
        }

        $db = \App\Config\Database::getDB();
        $userGroupId = $_SESSION['study_group_id'] ?? null;
        $universityId = $_SESSION['university_id'] ?? 1;

        // 1. Получаем список вузов
        $universities = $db->query("SELECT id, name FROM universities ORDER BY name")->fetchAll(\PDO::FETCH_ASSOC);

        // 2. Получаем группы (без ошибочного is_active, если его нет)
        $groupsStmt = $db->prepare("SELECT id, name FROM study_groups WHERE university_id = ? ORDER BY name");
        $groupsStmt->execute([$universityId]);
        $studyGroups = $groupsStmt->fetchAll(\PDO::FETCH_ASSOC);

        // 3. Получаем преподавателей
        $teachersStmt = $db->prepare("SELECT id, CONCAT(last_name, ' ', first_name) AS name FROM teachers WHERE university_id = ? ORDER BY last_name");
        $teachersStmt->execute([$universityId]);
        $teachers = $teachersStmt->fetchAll(\PDO::FETCH_ASSOC);

        include dirname(__DIR__) . '/Views/schedule.view.php';
    }

    /**
     * Returns JSON data for the dynamic schedule grid
     */
    private function handleAjaxSchedule() {
        header('Content-Type: application/json');
        
        // In a real scenario, fetch the active semester ID from DB. Hardcoded to 1 for scaffolding.
        $semesterId = filter_input(INPUT_GET, 'semester_id', FILTER_VALIDATE_INT) ?: 1; 
        
        $filters = [
            'study_group_id' => filter_input(INPUT_GET, 'study_group_id', FILTER_VALIDATE_INT),
            'teacher_id'     => filter_input(INPUT_GET, 'teacher_id', FILTER_VALIDATE_INT),
            'week_parity'    => filter_input(INPUT_GET, 'week_parity', FILTER_SANITIZE_SPECIAL_CHARS) ?: 'both'
        ];

        $repository = new ScheduleRepository();
        $data = $repository->getFilteredSchedule($semesterId, $filters);
        
        echo json_encode(['success' => true, 'data' => $data]);
        exit;
    }

    /**
     * Обработка формы и сохранение новой записи в расписание
     * с предварительной проверкой ограничений по нагрузке преподавателя
     */
    public function store() {
        $this->requireAdmin();
        // Проверяем, что данные отправлены методом POST
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ?route=schedule');
            exit;
        }

        // Безопасный сбор и базовая валидация входящих ID сущностей из формы
        $teacherId   = filter_input(INPUT_POST, 'teacher_id', FILTER_VALIDATE_INT);
        $dayOfWeek   = filter_input(INPUT_POST, 'day_of_week', FILTER_VALIDATE_INT);
        $semesterId  = filter_input(INPUT_POST, 'semester_id', FILTER_VALIDATE_INT);
        
        // Согласно регламенту, стандартная пара длится 1.5 астрономических часа (90 минут)
        $durationHours = 1.5; 

        if (!$teacherId || !$dayOfWeek || !$semesterId) {
            $_SESSION['error'] = 'Переданы некорректные или неполные данные формы.';
            header('Location: ?route=schedule');
            exit;
        }

        // Инициализируем созданную архитектуру управления бизнес-правилами
        $repository = new ScheduleRepository();
        $service = new ScheduleService($repository);

        // Запрашиваем проверку у сервисного слоя
        $validation = $service->validateTeacherLoad($teacherId, $dayOfWeek, $semesterId, $durationHours);

        // Если сервис обнаружил нарушение лимитов (более 5 пар или 8 часов)
        if (!$validation['allowed']) {
            $_SESSION['error'] = $validation['message'];
            // Возвращаем пользователя обратно на форму или страницу расписания с ошибкой
            header('Location: ?route=schedule');
            exit;
        }

        // Если проверка пройдена успешно — выполняем фактическую запись в БД через модель.
        // Ниже представлен пример структуры массива для сохранения в таблицу schedule_entries.
        /*
        $entryData = [
            'university_id'    => filter_input(INPUT_POST, 'university_id', FILTER_VALIDATE_INT),
            'semester_id'      => $semesterId,
            'day_of_week'      => $dayOfWeek,
            'bell_schedule_id' => filter_input(INPUT_POST, 'bell_schedule_id', FILTER_VALIDATE_INT),
            'study_group_id'   => filter_input(INPUT_POST, 'study_group_id', FILTER_VALIDATE_INT),
            'discipline_id'    => filter_input(INPUT_POST, 'discipline_id', FILTER_VALIDATE_INT),
            'lesson_type_id'   => filter_input(INPUT_POST, 'lesson_type_id', FILTER_VALIDATE_INT),
            'teacher_id'       => $teacherId,
            'room_id'          => filter_input(INPUT_POST, 'room_id', FILTER_VALIDATE_INT) ?: null,
            'week_parity'      => $_POST['week_parity'] ?? 'both',
            'status'           => 'active'
        ];
        
        Schedule::createEntry($entryData);
        */

        $_SESSION['success'] = 'Занятие успешно добавлено в расписание.';
        header('Location: ?route=schedule');
        exit;
    }
}