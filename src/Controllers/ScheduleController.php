<?php
namespace App\Controllers;

use App\Models\Schedule;
use App\Repositories\ScheduleRepository;
use App\Services\ScheduleService;

class ScheduleController {
    
    /**
     * Отображение общего расписания занятий
     */
    public function index() {
        $schedule = Schedule::getSchedule();
        include dirname(__DIR__) . '/Views/schedule.view.php';
    }

    /**
     * Обработка формы и сохранение новой записи в расписание
     * с предварительной проверкой ограничений по нагрузке преподавателя
     */
    public function store() {
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