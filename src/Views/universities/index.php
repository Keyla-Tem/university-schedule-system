<?php 
// 1. Необязательно, но круто: задаем имя вкладки. 
// Переменная $pageTitle подставится в тег <title> внутри нашего header.php
$pageTitle = "Университеты - KeySchedule"; 

// 2. ПОДКЛЮЧАЕМ ШАПКУ И СТИЛИ
// dirname(__DIR__) автоматически выведет PHP из папки "universities" на один уровень вверх — в папку "Views"
require_once dirname(__DIR__) . '/layout/header.php'; 
?>

<div class="header">
    <h1>Университеты</h1>
</div>

<div class="controls">
    <a href="?route=universities&action=create" class="btn">Добавить университет</a>
</div>

<div class="schedule-table-wrapper">
    <table class="schedule-table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Название вуза</th>
                <th>Короткое название</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($universities as $uni): ?>
                <tr>
                    <td><?= $uni['id'] ?></td>
                    <td><?= htmlspecialchars($uni['name']) ?></td>
                    <td><?= htmlspecialchars($uni['short_name']) ?></td>
                </tr>
            <?php endforeach; ?> </tbody>
    </table>
</div>

<?php 
// Подключаем подвал сайта
require_once dirname(__DIR__) . '/layout/footer.php'; 
?>