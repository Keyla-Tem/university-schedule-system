<?php include $_SERVER['DOCUMENT_ROOT'] . '/keyschedule/src/Views/layout/header.php'; ?>

<link rel="stylesheet" href="/keyschedule/public/css/schedule.css">

<div class="schedule-container">
    <header class="schedule-header">
        <h1>Расписание занятий</h1>
        
        <div class="schedule-filters">
            <select id="group-filter" class="filter-select">
                <option value="">-- Выберите группу --</option>
                <?php foreach ($studyGroups as $group): ?>
                    <option value="<?= $group['id'] ?>" <?= $group['id'] == $userGroupId ? 'selected' : '' ?>>
                        <?= htmlspecialchars($group['name']) ?>
                    </option>
                <?php endforeach; ?>
            </select>

            <select id="teacher-filter" class="filter-select">
                <option value="">-- Выберите преподавателя --</option>
                <?php foreach ($teachers as $teacher): ?>
                    <option value="<?= $teacher['id'] ?>">
                        <?= htmlspecialchars($teacher['name']) ?>
                    </option>
                <?php endforeach; ?>
            </select>

            <select id="parity-filter" class="filter-select">
                <option value="both">Все недели</option>
                <option value="odd">Числитель (нечетная)</option>
                <option value="even">Знаменатель (четная)</option>
            </select>
        </div>
    </header>

    <div class="schedule-grid" id="schedule-grid">
        <div class="loading-spinner">Загрузка данных расписания...</div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const grid = document.getElementById('schedule-grid');
    const groupFilter = document.getElementById('group-filter');
    const teacherFilter = document.getElementById('teacher-filter');
    const parityFilter = document.getElementById('parity-filter');

    const daysOfWeek = ['Понедельник', 'Вторник', 'Среда', 'Четверг', 'Пятница', 'Суббота'];

    /**
     * Запрос данных через AJAX (метод handleAjaxSchedule в контроллере)
     */
    const fetchSchedule = async () => {
        const groupId = groupFilter.value;
        const teacherId = teacherFilter.value;
        const parity = parityFilter.value;

        grid.innerHTML = '<div class="loading-spinner">Загрузка...</div>';

        // URL строится на основе твоего маршрутизатора
        const url = `?route=schedule&ajax=1&study_group_id=${groupId}&teacher_id=${teacherId}&week_parity=${parity}`;

        try {
            const response = await fetch(url);
            const result = await response.json();
            
            if (result.success) {
                renderGrid(result.data, groupId);
            } else {
                grid.innerHTML = '<p class="error-msg">Ошибка загрузки данных.</p>';
            }
        } catch (error) {
            grid.innerHTML = '<p class="error-msg">Ошибка сети. Проверьте консоль.</p>';
        }
    };

    /**
     * Отрисовка колонок и карточек занятий
     */
    const renderGrid = (lessons, selectedGroupId) => {
        grid.innerHTML = '';

        for (let i = 1; i <= 6; i++) {
            const col = document.createElement('div');
            col.className = 'day-column';
            
            const header = document.createElement('div');
            header.className = 'day-header';
            header.textContent = daysOfWeek[i - 1];
            col.appendChild(header);

            const dayLessons = lessons.filter(l => parseInt(l.day_of_week) === i);

            if (dayLessons.length === 0) {
                col.innerHTML += '<p style="color:#adb5bd; text-align:center;">Нет занятий</p>';
            } else {
                dayLessons.forEach(lesson => {
                    const card = document.createElement('div');
                    card.className = 'lesson-card';
                    
                    const timeRange = `${lesson.start_time.substring(0, 5)} - ${lesson.end_time.substring(0, 5)}`;
                    
                    card.innerHTML = `
                        <div class="lesson-time">${timeRange} (Пара ${lesson.pair_number})</div>
                        <div class="lesson-title">${lesson.discipline_name}</div>
                        <div class="lesson-meta">
                            <div>Тип: ${lesson.lesson_type_name}</div>
                            <div>Преподаватель: ${lesson.teacher_short_name}</div>
                            <div>Аудитория: ${lesson.room_number || 'н/д'}</div>
                        </div>
                    `;
                    col.appendChild(card);
                });
            }
            grid.appendChild(col);
        }
    };

    // Слушатели событий
    groupFilter.addEventListener('change', () => { teacherFilter.value = ''; fetchSchedule(); });
    teacherFilter.addEventListener('change', () => { groupFilter.value = ''; fetchSchedule(); });
    parityFilter.addEventListener('change', fetchSchedule);

    fetchSchedule();
});
</script>

<?php include $_SERVER['DOCUMENT_ROOT'] . '/keyschedule/src/Views/layout/footer.php'; ?>