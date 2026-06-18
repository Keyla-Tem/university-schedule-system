<?php
// index.php - Главная страница

// Подключаем конфигурацию и функции
require_once 'config.php';
require_once 'functions.php';

// Подключаем шапку
require_once 'header.php';

// Выводим таблицу расписания
require_once 'schedule_table.php';

// Подключаем подвал
require_once 'footer.php';
?>