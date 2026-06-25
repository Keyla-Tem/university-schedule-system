<?php include dirname(__DIR__, 2) . '/layout/header.php'; ?>
<div class="container">
    <h1>Список преподавателей</h1>
    <table class="schedule-table">
        <thead>
            <tr>
                <th>ФИО Преподавателя</th>
                <th>Должность</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($teachers as $teacher): ?>
            <tr>
                <td><?= htmlspecialchars($teacher['last_name'] . ' ' . $teacher['first_name'] . ' ' . $teacher['middle_name']) ?></td>
                <td><?= htmlspecialchars($teacher['position']) ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
<?php include dirname(__DIR__, 2) . '/layout/footer.php'; ?>