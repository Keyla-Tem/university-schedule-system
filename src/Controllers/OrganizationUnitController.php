<?php

namespace App\Controllers;

use App\Models\OrganizationUnit;
use App\Models\University;

class OrganizationUnitController extends BaseController
{
    private OrganizationUnit $unitModel;
    private University $universityModel;

    public function __construct()
    {
        $this->unitModel = new OrganizationUnit();
        $this->universityModel = new University();
    }

    public function index(): void
    {
        $this->requireLogin();

        // Привязываем список подразделений к конкретному ВУЗу
        $universityId = (int)($_GET['university_id'] ?? 0);
        $university = $this->universityModel->getById($universityId);

        if (!$university) {
            header('Location: index.php?route=universities');
            exit;
        }

        $units = $this->unitModel->getAllByUniversity($universityId);
        require_once dirname(__DIR__) . '/Views/units/index.php';
    }

    public function create(): void
    {
        $this->requireAdmin();
        $universityId = (int)($_GET['university_id'] ?? 0);
        $university = $this->universityModel->getById($universityId);
        
        // Получаем потенциальных родителей для выстраивания иерархии
        $existingUnits = $this->unitModel->getAllByUniversity($universityId);

        require_once dirname(__DIR__, 2) . '/views/units/create.php';
    }

    public function store(): void
    {
        $this->requireAdmin();
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $universityId = (int)($_POST['university_id'] ?? 0);
            $parentId = $_POST['parent_id'] !== '' ? (int)$_POST['parent_id'] : null;
            $name = trim(htmlspecialchars($_POST['name'] ?? ''));
            $shortName = trim(htmlspecialchars($_POST['short_name'] ?? ''));
            $unitType = htmlspecialchars($_POST['unit_type'] ?? 'other');

            if (!empty($name) && $universityId > 0) {
                $this->unitModel->create($universityId, $parentId, $name, $shortName ?: null, $unitType);
            }
            header('Location: index.php?route=units&university_id=' . $universityId);
            exit;
        }
    }

    public function edit(): void
    {
        $this->requireAdmin();
        $id = (int)($_GET['id'] ?? 0);
        $unit = $this->unitModel->getById($id);

        if (!$unit) {
            header('Location: index.php?route=universities');
            exit;
        }

        $university = $this->universityModel->getById($unit['university_id']);
        $existingUnits = $this->unitModel->getAllByUniversity($unit['university_id']);

        require_once dirname(__DIR__, 2) . '/views/units/edit.php';
    }

    public function update(): void
    {
        $this->requireAdmin();
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = (int)($_POST['id'] ?? 0);
            $universityId = (int)($_POST['university_id'] ?? 0);
            $parentId = $_POST['parent_id'] !== '' ? (int)$_POST['parent_id'] : null;
            $name = trim(htmlspecialchars($_POST['name'] ?? ''));
            $shortName = trim(htmlspecialchars($_POST['short_name'] ?? ''));
            $unitType = htmlspecialchars($_POST['unit_type'] ?? 'other');

            if (!empty($name) && $id > 0) {
                $this->unitModel->update($id, $parentId, $name, $shortName ?: null, $unitType);
            }
            header('Location: index.php?route=units&university_id=' . $universityId);
            exit;
        }
    }

    public function delete(): void
    {
        $this->requireAdmin();
        $id = (int)($_GET['id'] ?? 0);
        $universityId = (int)($_GET['university_id'] ?? 0);
        
        if ($id > 0) {
            $this->unitModel->delete($id);
        }
        header('Location: index.php?route=units&university_id=' . $universityId);
        exit;
    }
}