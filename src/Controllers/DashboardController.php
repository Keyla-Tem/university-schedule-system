<?php
namespace App\Controllers;

class DashboardController {
    public function index() {
        // Здесь потом добавим логику получения статистики (кол-во учителей, групп и т.д.)
        include dirname(__DIR__) . '/Views/dashboard.view.php';
    }
}