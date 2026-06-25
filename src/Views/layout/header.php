<?php
// src/Views/layout/header.php
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= \App\Config\AppConfig::get('APP_NAME', 'Система расписания') ?></title>
    
    <link rel="stylesheet" href="/keyschedule/public/css/reset.css">
    <link rel="stylesheet" href="/keyschedule/public/css/variables.css">
    <link rel="stylesheet" href="/keyschedule/public/css/layout.css">
    <link rel="stylesheet" href="/keyschedule/public/css/header.css">
    <link rel="stylesheet" href="/keyschedule/public/css/controls.css">
    <link rel="stylesheet" href="/keyschedule/public/css/table.css">
    <link rel="stylesheet" href="/keyschedule/public/css/lesson.css">
    <link rel="stylesheet" href="/keyschedule/public/css/responsive.css">
    <link rel="stylesheet" href="/keyschedule/public/css/sidebar.css">
</head>
<body>

<div class="app-layout">
    <aside class="sidebar">
        <div class="sidebar-header">📅 Расписание Университета</div>
        <ul class="nav-menu">
            <li><a href="index.php?route=dashboard">🏠 Главная</a></li>
            <li><a href="index.php?route=curriculums">📚 Учебные планы</a></li>
            <li><a href="index.php?route=groups">👥 Учебные группы</a></li>
            <li><a href="index.php?route=teachers">👨‍🏫 Преподаватели</a></li>
            <li><a href="index.php?route=units">🏢 Подразделения</a></li>
            <li><a href="index.php?route=classrooms">🚪 Аудитории</a></li>
            <li><a href="index.php?route=buildings">🏫 Корпуса</a></li>
            <li><a href="index.php?route=schedule">📅 Расписание</a></li>
            <li><a href="index.php?route=reports">📊 Отчеты</a></li>
            <li><a href="index.php?route=settings">⚙️ Настройки</a></li>
        </ul>
    </aside>

    <main class="main-content">
        <div class="container">