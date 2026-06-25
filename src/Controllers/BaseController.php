<?php

namespace App\Controllers;

abstract class BaseController
{
    protected function requireLogin(): void
    {
        if (!isset($_SESSION['user_id'])) {
            header("Location: index.php?route=login");
            exit;
        }
    }

    protected function requireAdmin(): void
    {
        $this->requireLogin();

        if (($_SESSION['role'] ?? '') !== 'admin') {
            http_response_code(403);
            die('403 Forbidden');
        }
    }
}