<?php
namespace App\Controllers;
use App\Models\StudyGroup;

class GroupController {
    public function index() {
        $groups = StudyGroup::getAll();
        include dirname(__DIR__) . '/Views/units/groups.view.php';
    }
}