<?php include dirname(__DIR__, 2) . '/layout/header.php'; ?>
<div class="container">
    <h1>Список учебных групп</h1>
    <table class="schedule-table">
        <thead>
            <tr>
                <th>Название группы</th>
                <th>Кол-во студентов</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($groups as $group): ?>
            <tr>
                <td><?= htmlspecialchars($group['name']) ?></td>
                <td><?= htmlspecialchars($group['student_count']) ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
<?php include dirname(__DIR__, 2) . '/layout/footer.php'; ?>