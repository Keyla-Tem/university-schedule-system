<?php
// Вспомогательная функция безопасного вывода (переедет потом в глобальный хелпер)
if (!function_exists('e')) {
    function e($str) {
        return htmlspecialchars($str ?? '', ENT_QUOTES, 'UTF-8');
    }
}
?>

<div class="page-content">
    <h2>Аудитории университета</h2>

    <div class="list-grid">
        <?php foreach ($rooms as $room): ?>
            <a href="index.php?page=classrooms&id=<?= $room['id'] ?>" 
               class="list-card <?= (isset($_GET['id']) && $_GET['id'] == $room['id']) ? 'active' : '' ?>">
                <strong><?= e($room['room_number']) ?></strong>
                <span class="sub"><?= e($room['building_name']) ?></span>
                <span class="badge"><?= $room['lesson_count'] ?> пар</span>
            </a>
        <?php endforeach; ?>
    </div>

    <?php if ($selected_room): ?>
        <h3>Расписание аудитории <?= e($selected_room['room_number']) ?> (<?= e($selected_room['building_name']) ?>)</h3>
        <p class="meta-info">Тип: <?= e($selected_room['type'] ?? 'Общая') ?> | Вместимость: <?= $selected_room['capacity'] ?> чел.</p>

        <?php if (empty($room_schedule)): ?>
            <div class="empty-state">В этой аудитории пока нет занятий!</div>
        <?php else: ?>
            <table class="schedule-table" style="margin-top: 15px; width: 100%; border-collapse: collapse;">
                <thead>
                    <tr>
                        <th>День</th>
                        <th>Пара</th>
                        <th>Время</th>
                        <th>Дисциплина</th>
                        <th>Тип</th>
                        <th>Преподаватель</th>
                        <th>Группа</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($room_schedule as $row): ?>
                        <tr>
                            <td><?= e($days[$row['day_of_week']] ?? $row['day_of_week']) ?></td>
                            <td><?= $row['pair_number'] ?></td>
                            <td>
                                <?php if ($row['start_time']): ?>
                                    <?= date('H:i', strtotime($row['start_time'])) ?>–<?= date('H:i', strtotime($row['end_time'])) ?>
                                <?php else: ?>—<?php endif; ?>
                            </td>
                            <td>
                                <?= e($row['discipline_name'] ?? '—') ?>
                                <?php if ($row['week_parity'] !== 'all'): ?>
                                    <span class="week-parity">(<?= $row['week_parity'] === 'odd' ? 'неч' : 'чет' ?>)</span>
                                <?php endif; ?>
                            </td>
                            <td><?= e($row['lesson_type'] ?? '—') ?></td>
                            <td><?= e($row['teacher_name'] ?? '—') ?></td>
                            <td><?= e($row['group_name'] ?? '—') ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    <?php endif; ?>
</div>