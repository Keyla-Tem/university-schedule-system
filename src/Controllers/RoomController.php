<?php

namespace App\Controllers;

use App\Models\Room;

class RoomController
{
    public function index(): void
    {
        // 1. Получаем ID университета из сессии
        $universityId = $_SESSION['university_id'] ?? 1;

        // 2. Инициализируем модель
        $roomModel = new Room();

        // 3. Базовые данные для вывода дней недели 
        
        $days = ['ПН', 'ВТ', 'СР', 'ЧТ', 'ПТ', 'СБ'];

        // 4. Логика: если выбран ID конкретной аудитории, показываем её расписание
        $selected_room = null;
        $room_schedule = [];
        
        if (isset($_GET['id']) && is_numeric($_GET['id'])) {
            $roomId = (int)$_GET['id'];
            $selected_room = $roomModel->getById($roomId);
            if ($selected_room) {
                $room_schedule = $roomModel->getSchedule($roomId, $universityId);
            }
        }

        // 5. В любом случае получаем список всех аудиторий для сайдбара/списка
        $rooms = $roomModel->getAllByUniversity($universityId);

        // 6. Подключаем верстку (Представление)
        // Внутри представления будут доступны все переменные: $rooms, $selected_room, $room_schedule, $days
        require_once dirname(__DIR__, 2) . '/src/Views/rooms.view.php';
    }
}