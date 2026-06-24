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

    <!-- <link rel="stylesheet" href="/css/reset.css">
    <link rel="stylesheet" href="/css/variables.css">
    <link rel="stylesheet" href="/css/layout.css">
    <link rel="stylesheet" href="/css/header.css">
    <link rel="stylesheet" href="/css/controls.css">
    <link rel="stylesheet" href="/css/table.css">
    <link rel="stylesheet" href="/css/lesson.css">
    <link rel="stylesheet" href="/css/responsive.css"> -->
</head>
<body>
<div class="container">
    <nav class="page-nav" style="padding: 15px 0; display: flex; gap: 10px;">
        <a href="index.php?route=schedule">Расписание</a>
        <a href="index.php?route=classrooms">Аудитории</a>
        <a href="index.php?route=groups">Группы</a>
        <a href="index.php?route=teachers">Преподаватели</a>
        <a href="index.php?route=universities">Университеты</a>
    </nav>