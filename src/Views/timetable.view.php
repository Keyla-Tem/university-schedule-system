<?php

if (!function_exists('e')) {
    function e($value)
    {
        return htmlspecialchars($value ?? '', ENT_QUOTES, 'UTF-8');
    }
}

$days = [
    1 => 'Понедельник',
    2 => 'Вторник',
    3 => 'Среда',
    4 => 'Четверг',
    5 => 'Пятница',
    6 => 'Суббота',
    7 => 'Воскресенье'
];

include dirname(__DIR__) . '/layout/header.php';
?>

<div class="page-content">

    <h2>Моё расписание</h2>

    <?php if (!$settingsCompleted): ?>

        <div class="empty-state">

            <h3>Расписание недоступно</h3>

            <p style="margin-top:10px;">
                Для просмотра расписания необходимо выбрать
                университет и учебную группу.
            </p>

            <a
                href="index.php?route=settings"
                class="btn btn-primary"
                style="margin-top:20px; display:inline-block;"
            >
                Перейти в настройки
            </a>

        </div>

    <?php elseif (empty($lessons)): ?>

        <div class="empty-state">

            Для выбранной группы расписание отсутствует.

        </div>

    <?php else: ?>

        <?php foreach ($days as $dayNumber => $dayName): ?>

            <?php

            $dayLessons = array_filter(
                $lessons,
                fn($lesson) => $lesson['day_of_week'] == $dayNumber
            );

            if (empty($dayLessons)) {
                continue;
            }

            ?>

            <h3 style="margin-top:30px;">
                <?= $dayName ?>
            </h3>

            <table class="schedule-table">

                <thead>

                <tr>
                    <th>Пара</th>
                    <th>Время</th>
                    <th>Дисциплина</th>
                    <th>Тип</th>
                    <th>Преподаватель</th>
                    <th>Аудитория</th>
                </tr>

                </thead>

                <tbody>

                <?php foreach ($dayLessons as $lesson): ?>

                    <tr>

                        <td>

                            <?= $lesson['pair_number'] ?>

                            <?php if ($lesson['week_parity'] != 'both'): ?>

                                <br>

                                <small>

                                    <?= $lesson['week_parity'] == 'odd'
                                        ? 'нечётная'
                                        : 'чётная' ?>

                                </small>

                            <?php endif; ?>

                        </td>

                        <td>

                            <?= date('H:i', strtotime($lesson['start_time'])) ?>

                            —

                            <?= date('H:i', strtotime($lesson['end_time'])) ?>

                        </td>

                        <td>

                            <?= e($lesson['discipline_name']) ?>

                        </td>

                        <td>

                            <?= e($lesson['lesson_type']) ?>

                        </td>

                        <td>

                            <?= e($lesson['teacher_name']) ?>

                        </td>

                        <td>

                            <?= e($lesson['room_number']) ?>

                        </td>

                    </tr>

                <?php endforeach; ?>

                </tbody>

            </table>

        <?php endforeach; ?>

    <?php endif; ?>

</div>

<?php include dirname(__DIR__) . '/layout/footer.php'; ?>