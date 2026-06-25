<?php
namespace App\Controllers;
use App\Models\Schedule;

class ScheduleController {
    public function index() {
        $schedule = Schedule::getSchedule();
        include dirname(__DIR__) . '/Views/schedule.view.php';
    }
}