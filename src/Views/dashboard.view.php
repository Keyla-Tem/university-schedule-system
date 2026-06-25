<?php include $_SERVER['DOCUMENT_ROOT'] . '/keyschedule/src/Views/layout/header.php'; ?>

<div class="container">
    <h1>Панель управления</h1>
    <div class="dashboard-grid" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 20px; margin-top: 20px;">
        
        <a href="?route=schedule" style="padding: 20px; background: #f4f4f4; text-decoration: none; color: #333; border: 1px solid #ddd; border-radius: 8px;">
            <h3>Расписание</h3>
            <p>Просмотр и редактирование занятий</p>
        </a>

        <a href="?route=teachers" style="padding: 20px; background: #f4f4f4; text-decoration: none; color: #333; border: 1px solid #ddd; border-radius: 8px;">
            <h3>Преподаватели</h3>
            <p>Управление списком сотрудников</p>
        </a>

        <a href="?route=groups" style="padding: 20px; background: #f4f4f4; text-decoration: none; color: #333; border: 1px solid #ddd; border-radius: 8px;">
            <h3>Группы</h3>
            <p>Учебные и академические группы</p>
        </a>
        
    </div>
</div>

<?php include $_SERVER['DOCUMENT_ROOT'] . '/keyschedule/src/Views/layout/footer.php'; ?>