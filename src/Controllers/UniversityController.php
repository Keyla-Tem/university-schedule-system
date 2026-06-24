<?php

namespace App\Controllers;

use App\Models\University;

class UniversityController
{
    private University $universityModel;

    public function __construct()
    {
        $this->universityModel = new University();
    }

    public function index(): void
    {
        $universities = $this->universityModel->getAll();
        require_once dirname(__DIR__) . '/Views/universities/index.php';
    }

    public function create(): void
    {
        require_once dirname(__DIR__) . '/Views/universities/create.php';
    }

    public function store(): void
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Фильтрация входных данных
            $name = trim(htmlspecialchars($_POST['name'] ?? ''));
            $shortName = trim(htmlspecialchars($_POST['short_name'] ?? ''));

            if (!empty($name)) {
                $this->universityModel->create($name, $shortName ?: null);
            }
        }
        header('Location: index.php?route=universities');
        exit;
    }

    public function edit(): void
    {
        $id = (int)($_GET['id'] ?? 0);
        $university = $this->universityModel->getById($id);

        if (!$university) {
            header('Location: index.php?route=universities');
            exit;
        }

        require_once dirname(__DIR__, 2) . '/views/universities/edit.php';
    }

    public function update(): void
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = (int)($_POST['id'] ?? 0);
            $name = trim(htmlspecialchars($_POST['name'] ?? ''));
            $shortName = trim(htmlspecialchars($_POST['short_name'] ?? ''));

            if (!empty($name) && $id > 0) {
                $this->universityModel->update($id, $name, $shortName ?: null);
            }
        }
        header('Location: index.php?route=universities');
        exit;
    }

    public function delete(): void
    {
        $id = (int)($_GET['id'] ?? 0);
        if ($id > 0) {
            $this->universityModel->delete($id);
        }
        header('Location: index.php?route=universities');
        exit;
    }
}