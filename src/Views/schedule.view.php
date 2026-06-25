<?php include $_SERVER['DOCUMENT_ROOT'] . '/keyschedule/src/Views/layout/header.php'; ?>

<div class="container">
    <h1>Расписание занятий</h1>
    <table class="schedule-table">
        <thead>
            <tr>
                <th>День</th>
                <th>Время</th>
                <th>Предмет</th>
                <th>Преподаватель</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($schedule as $item): ?>
            <tr>
                <td><?= htmlspecialchars($item['day_of_week']) ?></td>
                <td><?= htmlspecialchars($item['start_time']) ?></td>
                <td><?= htmlspecialchars($item['subject_name']) ?></td>
                <td><?= htmlspecialchars($item['teacher_name']) ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<?php include $_SERVER['DOCUMENT_ROOT'] . '/keyschedule/src/Views/layout/footer.php'; ?>