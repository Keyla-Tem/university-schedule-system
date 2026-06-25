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

        // ============================================================
        // ОБРАБОТКА ПОСТ-ЗАПРОСОВ (CRUD ЛОГИКА)
        // ============================================================
        
        // Обработка добавления новой аудитории
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'create_room') {
            $buildingId = (int)($_POST['building_id'] ?? 0);
            $roomTypeId = (int)($_POST['room_type_id'] ?? 0);
            $roomNumber = trim($_POST['room_number'] ?? '');
            $capacity = (int)($_POST['capacity'] ?? 0);
            $notes = trim($_POST['notes'] ?? '');

            // Базовая валидация: номер не пустой, корпус и тип выбраны
            if (!empty($roomNumber) && $buildingId > 0 && $roomTypeId > 0) {
                $roomModel->create($universityId, $buildingId, $roomTypeId, $roomNumber, $capacity, $notes);
                
                // Перенаправляем на ту же страницу, чтобы сбросить отправку формы при обновлении (F5)
                header("Location: " . BASE_URL . "/index.php?route=classrooms");
                exit;
            }
        }

        // Обработка удаления аудитории
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'delete_room') {
            $roomId = (int)($_POST['room_id'] ?? 0);
            
            if ($roomId > 0) {
                $roomModel->delete($roomId);
                
                // Перенаправляем обратно на список комнат
                header("Location: " . BASE_URL . "/index.php?route=classrooms");
                exit;
            }
        }

        // ============================================================
        // ПОДГОТОВКА ДАННЫХ ДЛЯ ВЫВОДА
        // ============================================================

        $days = ['ПН', 'ВТ', 'СР', 'ЧТ', 'ПТ', 'СБ'];

        // Логика: если выбран ID конкретной аудитории, показываем её расписание
        $selected_room = null;
        $room_schedule = [];
        
        if (isset($_GET['id']) && is_numeric($_GET['id'])) {
            $roomId = (int)$_GET['id'];
            $selected_room = $roomModel->getById($roomId);
            if ($selected_room) {
                $room_schedule = $roomModel->getSchedule($roomId, $universityId);
            }
        }

        // В любом случае получаем список всех аудиторий для сайдбара/списка
        $rooms = $roomModel->getAllByUniversity($universityId);

        // Нам понадобятся списки корпусов и типов комнат для выпадающих списков в форме добавления.
        // Пока вытащим их временным костылем прямо через PDO, чтобы не создавать лишние модели сегодня.
        $db = \App\Config\Database::getInstance()->getConnection();
        
        // Получаем корпуса только текущего вуза
        $stmtBuildings = $db->prepare("SELECT id, name FROM buildings WHERE university_id = ? ORDER BY name");
        $stmtBuildings->execute([$universityId]);
        $buildings = $stmtBuildings->fetchAll();

        // Получаем все типы аудиторий
        $roomTypes = $db->query("SELECT id, name FROM room_types ORDER BY name")->fetchAll();

        // Подключаем верстку (Представление)
        // Теперь внутри шаблона доступны еще и $buildings, $roomTypes
        require_once dirname(__DIR__) . '/views/rooms.view.php';
    }
}