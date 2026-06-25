<?php
namespace App\Controllers;

class TeacherController {
    public function index() {
        $teachers = \App\Models\Teacher::getAll(); // Получаем данные
        include dirname(__DIR__) . '/Views/units/teachers.view.php'; // Передаем их в файл, где они станут переменной $teachers
    }
}