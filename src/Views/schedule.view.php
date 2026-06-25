<?php include $_SERVER['DOCUMENT_ROOT'] . '/keyschedule/src/Views/layout/header.php'; ?>

<link rel="stylesheet" href="/keyschedule/public/css/schedule.css">

<div class="schedule-container">
    <header class="schedule-header">
        <h1>Расписание занятий</h1>
        
        <div class="schedule-filters">
            <select id="university-filter" class="filter-select">
                <option value="">-- Выберите вуз --</option>
                <?php foreach ($universities as $uni): ?>
                    <option value="<?= $uni['id'] ?>"><?= htmlspecialchars($uni['name']) ?></option>
                <?php endforeach; ?>
            </select>

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
                    <option value="<?= $teacher['id'] ?>"><?= htmlspecialchars($teacher['name']) ?></option>
                <?php endforeach; ?>
            </select>

            <select id="parity-filter" class="filter-select">
                <option value="both">Любая неделя</option>
                <option value="odd">Нечетная</option>
                <option value="even">Четная</option>
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
    const uniFilter = document.getElementById('university-filter');
    const groupFilter = document.getElementById('group-filter');
    const teacherFilter = document.getElementById('teacher-filter');
    const parityFilter = document.getElementById('parity-filter');
    const daysOfWeek = ['Пн', 'Вт', 'Ср', 'Чт', 'Пт', 'Сб'];

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
            
            if (!lessons || lessons.length === 0) {
                grid.innerHTML = `
                    <div style="text-align: center; padding: 40px; color: #6c757d;">
                        <h3>Для отображения расписания выберите параметры поиска</h3>
                        <p>Укажите ВУЗ, группу или преподавателя в фильтрах выше.</p>
                    </div>
                `;
                return;
            }

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

    const fetchSchedule = async () => {
        if (!groupFilter) return;
        const uniId = uniFilter?.value || '';
        const groupId = groupFilter?.value || '';
        const teacherId = teacherFilter?.value || '';
        const parity = parityFilter?.value || 'both';

        grid.innerHTML = '<div class="loading-spinner">Загрузка данных...</div>';
        const url = `index.php?route=schedule&ajax=1&university_id=${uniId}&study_group_id=${groupId}&teacher_id=${teacherId}&week_parity=${parity}`;

        try {
            const response = await fetch(url);
            const text = await response.text();
            const result = JSON.parse(text);
            
            if (result.success) {
                renderGrid(result.data, groupId);
            } else {
                grid.innerHTML = '<p class="error-msg">Ошибка данных.</p>';
            }
        } catch (error) {
            console.error("Ошибка:", error);
            grid.innerHTML = '<p class="error-msg">Ошибка сети или сервера.</p>';
        }
    };

    [uniFilter, groupFilter, teacherFilter, parityFilter].forEach(el => {
        el?.addEventListener('change', fetchSchedule);
    });

    fetchSchedule();
});
</script>

<?php include $_SERVER['DOCUMENT_ROOT'] . '/keyschedule/src/Views/layout/footer.php'; ?>