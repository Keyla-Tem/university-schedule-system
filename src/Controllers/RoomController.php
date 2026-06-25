<?php

namespace App\Controllers;

use App\Models\Room;

class RoomController extends BaseController
{
    public function index(): void
    {
        // Просматривать могут все авторизованные пользователи
        $this->requireLogin();

        $universityId = $_SESSION['university_id'] ?? 1;
        $roomModel = new Room();

        // ============================================================
        // CRUD (ТОЛЬКО ADMIN)
        // ============================================================

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            // Любое изменение данных только для админа
            $this->requireAdmin();

            // Создание аудитории
            if (
                isset($_POST['action']) &&
                $_POST['action'] === 'create_room'
            ) {
                $buildingId = (int)($_POST['building_id'] ?? 0);
                $roomTypeId = (int)($_POST['room_type_id'] ?? 0);
                $roomNumber = trim($_POST['room_number'] ?? '');
                $capacity = (int)($_POST['capacity'] ?? 0);
                $notes = trim($_POST['notes'] ?? '');

                if (
                    !empty($roomNumber) &&
                    $buildingId > 0 &&
                    $roomTypeId > 0
                ) {
                    $roomModel->create(
                        $universityId,
                        $buildingId,
                        $roomTypeId,
                        $roomNumber,
                        $capacity,
                        $notes
                    );
                }

                header("Location: " . BASE_URL . "/index.php?route=classrooms");
                exit;
            }

            // Удаление аудитории
            if (
                isset($_POST['action']) &&
                $_POST['action'] === 'delete_room'
            ) {
                $roomId = (int)($_POST['room_id'] ?? 0);

                if ($roomId > 0) {
                    $roomModel->delete($roomId);
                }

                header("Location: " . BASE_URL . "/index.php?route=classrooms");
                exit;
            }
        }

        // ============================================================
        // ПРОСМОТР (ADMIN + USER)
        // ============================================================

        $days = ['ПН', 'ВТ', 'СР', 'ЧТ', 'ПТ', 'СБ'];

        $selected_room = null;
        $room_schedule = [];

        if (isset($_GET['id']) && is_numeric($_GET['id'])) {
            $roomId = (int)$_GET['id'];

            $selected_room = $roomModel->getById($roomId);

            if ($selected_room) {
                $room_schedule = $roomModel->getSchedule(
                    $roomId,
                    $universityId
                );
            }
        }

        // Все аудитории текущего университета
        $rooms = $roomModel->getAllByUniversity($universityId);

        $db = \App\Config\Database::getInstance()->getConnection();

        // Корпуса
        $stmtBuildings = $db->prepare(
            "SELECT id, name
             FROM buildings
             WHERE university_id = ?
             ORDER BY name"
        );

        $stmtBuildings->execute([$universityId]);
        $buildings = $stmtBuildings->fetchAll();

        // Типы аудиторий
        $roomTypes = $db->query(
            "SELECT id, name
             FROM room_types
             ORDER BY name"
        )->fetchAll();

        require_once dirname(__DIR__) . '/views/rooms.view.php';
    }
}