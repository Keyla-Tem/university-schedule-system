<?php
// schedule_table.php - Вывод таблицы расписания

$days = ['ПН', 'ВТ', 'СР', 'ЧТ', 'ПТ', 'СБ'];
$pairs = range(1, 8);

// Получаем данные
$scheduleData = getScheduleData($pdo);
$grid = groupSchedule($scheduleData);
$stats = getStats($pdo);
?>
<!-- ШАПКА -->
<header class="header">
    <h1>
        <?= APP_NAME ?>
        <span><?= e($stats['semester']) ?></span>
    </h1>
    <div class="header-info">
        <div class="stat">
            <span class="number"><?= $stats['lessons'] ?></span>
            <span class="label">Занятий</span>
        </div>
        <div class="stat">
            <span class="number"><?= $stats['groups'] ?></span>
            <span class="label">Групп</span>
        </div>
        <div class="stat">
            <span class="number"><?= $stats['teachers'] ?></span>
            <span class="label">Преподавателей</span>
        </div>
    </div>
</header>

<!-- ПАНЕЛЬ УПРАВЛЕНИЯ -->
<div class="controls">
    <div class="semester-badge">
        📅 <?= e($stats['semester']) ?>
    </div>
    <div class="legend">
        <span class="legend-item"><span class="dot dot-planned"></span> Планируется</span>
        <span class="legend-item"><span class="dot dot-confirmed"></span> Подтверждено</span>
        <span class="legend-item"><span class="dot dot-cancelled"></span> Отменено</span>
        <span class="legend-item"><span class="dot dot-rescheduled"></span> Перенесено</span>
    </div>
</div>

<!-- ТАБЛИЦА -->
<div class="schedule-table-wrapper">
    <table class="schedule-table">
        <thead>
            <tr>
                <th style="width:80px; min-width:80px;">
                    Пара
                    <span class="sub">Время</span>
                </th>
                <?php foreach ($days as $index => $day): ?>
                    <th>
                        <?= $day ?>
                        <span class="sub">День <?= $index + 1 ?></span>
                    </th>
                <?php endforeach; ?>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($pairs as $pair): ?>
                <tr>
                    <!-- Время -->
                    <td class="time-cell">
                        <span class="pair-num"><?= $pair ?></span>
                        <span class="time-range"><?= getPairTime($scheduleData, $pair) ?></span>
                    </td>

                    <!-- Дни недели -->
                    <?php foreach (range(1, 6) as $day): ?>
                        <td>
                            <?php if (isset($grid[$day][$pair])): ?>
                                <?php foreach ($grid[$day][$pair] as $lesson): ?>
                                    <div class="lesson">
                                        <span class="discipline">
                                            <?= e($lesson['discipline_name'] ?? 'Без названия') ?>
                                            <?php if ($lesson['week_parity'] !== 'all'): ?>
                                                <span class="week-parity">
                                                    (<?= $lesson['week_parity'] === 'odd' ? 'неч' : 'чет' ?>)
                                                </span>
                                            <?php endif; ?>
                                            <?php if ($lesson['discipline_code']): ?>
                                                <span class="code">[<?= e($lesson['discipline_code']) ?>]</span>
                                            <?php endif; ?>
                                        </span>
                                        <div class="meta">
                                            <span class="teacher">👨‍🏫 <?= e($lesson['teacher_name'] ?? '—') ?></span>
                                            <?php if ($lesson['room']): ?>
                                                <span class="room">📍 <?= e($lesson['room']) ?>
                                                    <?= $lesson['building'] ? ' (' . e($lesson['building']) . ')' : '' ?>
                                                </span>
                                            <?php endif; ?>
                                            <span class="type"><?= e($lesson['lesson_type'] ?? '') ?></span>
                                            <span class="group">👥 <?= e($lesson['group_name'] ?? '') ?></span>
                                        </div>
                                        
                                        <?php if ($lesson['status'] && $lesson['status'] !== 'planned'): ?>
                                            <span class="status-badge <?= getStatusClass($lesson['status']) ?>">
                                                <?= getStatusIcon($lesson['status']) ?>
                                                <?= e($lesson['status']) ?>
                                            </span>
                                        <?php endif; ?>
                                        
                                        <?php if ($lesson['notes']): ?>
                                            <div class="notes-text">
                                                📝 <?= e($lesson['notes']) ?>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <div class="empty-cell">—</div>
                            <?php endif; ?>
                        </td>
                    <?php endforeach; ?>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>