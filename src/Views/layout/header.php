<?php
// src/Views/layout/header.php
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= \App\Config\AppConfig::get('APP_NAME', 'Система расписания') ?></title>
    
    <link rel="stylesheet" href="<?= BASE_URL ?>/css/reset.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>/css/variables.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>/css/layout.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>/css/header.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>/css/controls.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>/css/table.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>/css/lesson.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>/css/responsive.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>/css/sidebar.css">
</head>
<body>

<div class="app-layout">
    <aside class="sidebar">

        <div class="sidebar-header">📅 Расписание Университета</div>

        <?php if (isset($_SESSION['user_id'])): ?>
        <div class="user-profile-block" style="padding: 15px; background: rgba(255, 255, 255, 0.05); border-radius: 6px; margin: 10px; border-left: 4px solid #6366f1;">
            <div style="font-size: 11px; color: #a5b4fc; text-transform: uppercase; letter-spacing: 0.5px;">Аккаунт:</div>
            <div style="font-weight: bold; color: #ffffff; font-size: 15px; margin-top: 2px; overflow: hidden; text-overflow: ellipsis;">
                👤 <?= htmlspecialchars($_SESSION['user_name'] ?? 'Пользователь') ?>
            </div>
            <div style="font-size: 11px; color: #94a3b8; margin-top: 4px;">
                Статус: <?= ($_SESSION['role'] ?? 'user') === 'admin' ? '<span style="color: #f87171; font-weight: bold;">🔴 Админ</span>' : '<span style="color: #4ade80;">🟢 Студент</span>' ?>
            </div>
        </div>
        <?php endif; ?>

        <ul class="nav-menu">
            <li><a href="index.php?route=schedule">🔎 Поиск</a></li>
            <li><a href="index.php?route=timetable">📅 Расписание</a></li>
            <li><a href="index.php?route=settings">⚙️ Настройки</a></li>

            <?php if (($_SESSION['role'] ?? 'user') === 'admin'): ?>
                <li style="margin-top: 20px; padding: 10px 0 5px 15px; color: #6366f1; font-size: 10px; text-transform: uppercase; font-weight: bold; letter-spacing: 1px; border-top: 1px solid rgba(255,255,255,0.05);">
                    Администрирование
                </li>
                <!-- <li><a href="index.php?route=curriculums">📚 Учебные планы</a></li> -->
                <li><a href="index.php?route=groups">👥 Учебные группы</a></li>
                <li><a href="index.php?route=teachers">👨‍🏫 Преподаватели</a></li>
                <li><a href="index.php?route=units">🏢 Подразделения</a></li>
                <li><a href="index.php?route=classrooms">🚪 Аудитории</a></li>
                <!-- <li><a href="index.php?route=buildings">🏫 Корпуса</a></li> -->
                <li><a href="index.php?route=reports">📊 Отчеты</a></li>
            <?php endif; ?>
            
            <?php if (isset($_SESSION['user_id'])): ?>
            <li style="margin-top: 25px; border-top: 1px solid rgba(255, 255, 255, 0.1); padding-top: 10px;">
                <a href="index.php?route=logout" style="color: #f87171;">🚪 Выйти</a>
            </li>
            <?php endif; ?>
        </ul>
    </aside>

    <main class="main-content">
        <div class="container">