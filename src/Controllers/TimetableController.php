<?php

namespace App\Controllers;

use App\Models\User;
use App\Models\Timetable;

class TimetableController extends BaseController
{
    public function index(): void
    {
        $this->requireLogin();

        $user = User::findById($_SESSION['user_id']);

        $lessons = [];

        $settingsCompleted = true;

        if (
            empty($user['university_id']) ||
            empty($user['study_group_id'])
        ) {

            $settingsCompleted = false;

        } else {

            $lessons = Timetable::getByGroup(
                (int)$user['university_id'],
                (int)$user['study_group_id']
            );
        }

        require_once dirname(__DIR__) . '/Views/timetable.view.php';
    }
}