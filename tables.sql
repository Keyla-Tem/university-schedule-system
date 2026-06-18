-- Удаляем и создаем БД заново
DROP DATABASE IF EXISTS university_schedule;
CREATE DATABASE university_schedule CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE university_schedule;

-- 1. Университеты
CREATE TABLE universities (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY COMMENT 'id, на него сслылается все в расписании одного уника',
    name VARCHAR(255) NOT NULL COMMENT 'Название ', 
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP COMMENT 'Даьа добавления в систему '
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 2. Пользователи (составители расписания) для входа 
CREATE TABLE users (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY COMMENT 'Id пользователя',
    university_id INT UNSIGNED NOT NULL,
    email VARCHAR(255) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    full_name VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (university_id) REFERENCES universities(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 3. Подразделения, сюда отсылается преподаватель (где работает) и направления ( кто ведет)
CREATE TABLE organization_units (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    university_id INT UNSIGNED NOT NULL,
    parent_id INT UNSIGNED NULL COMMENT ' ссылка на старшее подразделение',
    name VARCHAR(255) NOT NULL,
    unit_type ENUM('school', 'department', 'chair') NOT NULL,
    short_name VARCHAR(100) COMMENT ' для отчетов или визуала, например ИМКТ',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    -- нет двух одинаковых подразделений
    UNIQUE KEY uk_university_unit_name (university_id, name),
    FOREIGN KEY (university_id) REFERENCES universities(id) ON DELETE CASCADE,
    FOREIGN KEY (parent_id) REFERENCES organization_units(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 4. Расписание звонков, справочник
CREATE TABLE bell_schedules (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    university_id INT UNSIGNED NOT NULL,
    pair_number TINYINT UNSIGNED NOT NULL COMMENT ' номер пары',
    start_time TIME NOT NULL COMMENT ' Начало',
    end_time TIME NOT NULL COMMENT ' конец',
    UNIQUE KEY uk_university_pair (university_id, pair_number) COMMENT ' у одного вуза не может быть двух первых пар',
    FOREIGN KEY (university_id) REFERENCES universities(id) ON DELETE CASCADE,
    -- триггеры, номер пары от 1 до 8, время начала раньше времени окончания, длительность пары не больше 90 минут
    CONSTRAINT chk_bell_pair_number CHECK (pair_number BETWEEN 1 AND 8),
    CONSTRAINT chk_bell_time_order CHECK (start_time < end_time),
    CONSTRAINT chk_bell_duration CHECK (TIMEDIFF(end_time, start_time) <= '01:30:00')
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 5. Типы помещений (лк=екционка и тд). Сюда ссылаются кабинеты (rooms)
CREATE TABLE room_types (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL UNIQUE COMMENT ' уникальное название',
    description VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 6. Корпуса
CREATE TABLE buildings (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    university_id INT UNSIGNED NOT NULL,
    name VARCHAR(255) NOT NULL,
    address VARCHAR(500),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY uk_university_building (university_id, name),
    FOREIGN KEY (university_id) REFERENCES universities(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 7. Расстояния между корпусами
CREATE TABLE building_distances (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    university_id INT UNSIGNED NOT NULL,
    building_from_id INT UNSIGNED NOT NULL COMMENT ' откуда', 
    building_to_id INT UNSIGNED NOT NULL COMMENT ' куда',
    travel_time_minutes TINYINT UNSIGNED NOT NULL COMMENT 'перемещение в минутах ',
    UNIQUE KEY uk_building_pair (university_id, building_from_id, building_to_id),
    FOREIGN KEY (university_id) REFERENCES universities(id) ON DELETE CASCADE,
    FOREIGN KEY (building_from_id) REFERENCES buildings(id) ON DELETE CASCADE,
    FOREIGN KEY (building_to_id) REFERENCES buildings(id) ON DELETE CASCADE,
    -- триггер, различаются корпуса, перемещение не больше 90 минут
    CONSTRAINT chk_distance_different CHECK (building_from_id != building_to_id),
    CONSTRAINT chk_distance_time CHECK (travel_time_minutes BETWEEN 1 AND 90)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 8. Аудитории
CREATE TABLE rooms (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    university_id INT UNSIGNED NOT NULL,
    building_id INT UNSIGNED NULL,
    room_type_id INT UNSIGNED NOT NULL,
    room_number VARCHAR(50) NOT NULL COMMENT ' например А100',
    capacity INT UNSIGNED NOT NULL COMMENT ' вместимость',
    is_online BOOLEAN DEFAULT FALSE COMMENT ' онлайн',
    notes VARCHAR(500) COMMENT ' заметки, можно внести ссылку на онлайн',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY uk_university_room (university_id, room_number),
    FOREIGN KEY (university_id) REFERENCES universities(id) ON DELETE CASCADE,
    FOREIGN KEY (building_id) REFERENCES buildings(id) ON DELETE SET NULL,
    FOREIGN KEY (room_type_id) REFERENCES room_types(id) ON DELETE RESTRICT,
    -- проверка ненулевой вместимости
    CONSTRAINT chk_room_capacity CHECK (capacity > 0 AND capacity <= 9999)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 9. Направления подготовки, отсылается учебный план, группы
CREATE TABLE specialties (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    university_id INT UNSIGNED NOT NULL,
    organization_unit_id INT UNSIGNED NOT NULL,
    code VARCHAR(50) NOT NULL COMMENT ' 09.03.03',
    name VARCHAR(255) NOT NULL COMMENT ' название направления',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    -- у одного вуза нет двух направлений с одинаковым кодом
    UNIQUE KEY uk_university_specialty (university_id, code),
    FOREIGN KEY (university_id) REFERENCES universities(id) ON DELETE CASCADE,
    FOREIGN KEY (organization_unit_id) REFERENCES organization_units(id) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 10. Преподаватели
CREATE TABLE teachers (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    university_id INT UNSIGNED NOT NULL,
    organization_unit_id INT UNSIGNED NOT NULL,
    last_name VARCHAR(100) NOT NULL,
    first_name VARCHAR(100) NOT NULL,
    middle_name VARCHAR(100),
    degree VARCHAR(100) COMMENT ' степень',
    academic_title VARCHAR(100) COMMENT ' звание',
    position VARCHAR(100) NOT NULL COMMENT ' должность',
    -- загруженность макс
    max_hours_per_day TINYINT UNSIGNED DEFAULT 8,
    max_pairs_per_day TINYINT UNSIGNED DEFAULT 5,
    max_hours_per_week TINYINT UNSIGNED DEFAULT 36,
    is_active BOOLEAN DEFAULT TRUE COMMENT ' активен ли, или уволен/в отпуске',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (university_id) REFERENCES universities(id) ON DELETE CASCADE,
    FOREIGN KEY (organization_unit_id) REFERENCES organization_units(id) ON DELETE RESTRICT,
    -- проверка на часы
    CONSTRAINT chk_teacher_hours CHECK (max_hours_per_day BETWEEN 1 AND 8),
    CONSTRAINT chk_teacher_pairs CHECK (max_pairs_per_day BETWEEN 1 AND 5),
    CONSTRAINT chk_teacher_weekly CHECK (max_hours_per_week BETWEEN 1 AND 36)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
-- для поиска по фио
CREATE INDEX idx_teacher_name ON teachers(last_name, first_name, middle_name);

-- 11. Академические группы
CREATE TABLE academic_groups (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    university_id INT UNSIGNED NOT NULL,
    specialty_id INT UNSIGNED NOT NULL,
    name VARCHAR(100) NOT NULL COMMENT ' название группы',
    year_of_study TINYINT UNSIGNED NOT NULL COMMENT ' год обучения',
    semester_number TINYINT UNSIGNED NOT NULL COMMENT ' семестр, так как расписание составляется на семестр',
    student_count INT UNSIGNED NOT NULL COMMENT ' количество студентов',
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (university_id) REFERENCES universities(id) ON DELETE CASCADE,
    FOREIGN KEY (specialty_id) REFERENCES specialties(id) ON DELETE RESTRICT,
    -- нет групп с одинаковым названием
    UNIQUE KEY uk_university_group_name (university_id, name),
    -- проверка на год обучения, семестр, количество студентов
    CONSTRAINT chk_academic_year CHECK (year_of_study BETWEEN 1 AND 6),
    CONSTRAINT chk_academic_semester CHECK (semester_number BETWEEN 1 AND 12),
    CONSTRAINT chk_academic_students CHECK (student_count > 0 AND student_count <= 500)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 12. Семестры (от него зависит calendar_days)
CREATE TABLE semesters (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    university_id INT UNSIGNED NOT NULL,
    academic_year INT UNSIGNED NOT NULL COMMENT ' год начала',
    semester_number TINYINT UNSIGNED NOT NULL,
    start_date DATE NOT NULL COMMENT ' дата начала',
    end_date DATE NOT NULL COMMENT ' дата конца',
    total_weeks TINYINT UNSIGNED DEFAULT 18,
    name VARCHAR(255) COMMENT ' осенний 2024-2025',
    is_active BOOLEAN DEFAULT TRUE COMMENT 'не активен, если семестр прошел',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (university_id) REFERENCES universities(id) ON DELETE CASCADE,
    UNIQUE KEY uk_university_academic_year_semester (university_id, academic_year, semester_number),
    CONSTRAINT chk_semester_number CHECK (semester_number IN (1, 12)),
    CONSTRAINT chk_semester_weeks CHECK (total_weeks BETWEEN 16 AND 20),
    CONSTRAINT chk_semester_dates CHECK (end_date > start_date)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 13. Учебные группы
CREATE TABLE study_groups (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    university_id INT UNSIGNED NOT NULL,
    name VARCHAR(150) NOT NULL,
    group_type ENUM('academic', 'subgroup', 'stream', 'mixed') NOT NULL COMMENT 'academic (академическая), subgroup (подгруппа), stream (поток), mixed (смешанная)',
    student_count INT UNSIGNED NOT NULL COMMENT 'количество студентов',
    description VARCHAR(500) COMMENT 'например подгруппа для лабораторных работ',
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (university_id) REFERENCES universities(id) ON DELETE CASCADE,
    UNIQUE KEY uk_university_study_group_name (university_id, name),
    CONSTRAINT chk_study_group_students CHECK (student_count > 0 AND student_count <= 1000)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
-- для бытрого поиска по типу группы
CREATE INDEX idx_study_group_type ON study_groups(university_id, group_type);

-- 14. Состав учебных групп
CREATE TABLE study_group_composition (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    study_group_id INT UNSIGNED NOT NULL COMMENT 'какая учебная группа',
    academic_group_id INT UNSIGNED NOT NULL COMMENT 'какая академ группа входит в состав',
    student_portion INT UNSIGNED NULL COMMENT '0 - все студенты, но можно указать определенное количество',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (study_group_id) REFERENCES study_groups(id) ON DELETE CASCADE,
    FOREIGN KEY (academic_group_id) REFERENCES academic_groups(id) ON DELETE CASCADE,
    UNIQUE KEY uk_study_academic_pair (study_group_id, academic_group_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 15. Дисциплины
CREATE TABLE disciplines (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    university_id INT UNSIGNED NOT NULL,
    code VARCHAR(50) NOT NULL COMMENT 'код дисциаплины',
    name VARCHAR(255) NOT NULL,
    description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (university_id) REFERENCES universities(id) ON DELETE CASCADE,
    UNIQUE KEY uk_university_discipline_code (university_id, code)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE INDEX idx_discipline_name ON disciplines(university_id, name);

-- 16. Учебные планы
CREATE TABLE curriculums (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    university_id INT UNSIGNED NOT NULL,
    specialty_id INT UNSIGNED NOT NULL,
    academic_year_start YEAR NOT NULL,
    academic_year_end YEAR NOT NULL,
    semester_number TINYINT UNSIGNED NOT NULL,
    semester_weeks TINYINT UNSIGNED DEFAULT 18,
    description VARCHAR(500),
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (university_id) REFERENCES universities(id) ON DELETE CASCADE,
    FOREIGN KEY (specialty_id) REFERENCES specialties(id) ON DELETE RESTRICT,
    UNIQUE KEY uk_specialty_semester (specialty_id, semester_number, academic_year_start),
    CONSTRAINT chk_curriculum_semester CHECK (semester_number BETWEEN 1 AND 12),
    CONSTRAINT chk_curriculum_weeks CHECK (semester_weeks BETWEEN 16 AND 20)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 17. Дисциплины в учебном плане
CREATE TABLE curriculum_disciplines (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    curriculum_id INT UNSIGNED NOT NULL,
    discipline_id INT UNSIGNED NOT NULL,
    lecture_hours TINYINT UNSIGNED DEFAULT 0 COMMENT 'количестов лекций',
    practical_hours TINYINT UNSIGNED DEFAULT 0 COMMENT 'количестов практик',
    laboratory_hours TINYINT UNSIGNED DEFAULT 0 COMMENT 'лабораторных',
    independent_work_hours TINYINT UNSIGNED DEFAULT 0 COMMENT 'часы самостоятельной работы',
    assessment_type ENUM('exam', 'test', 'coursework', 'none') DEFAULT 'none',
    notes VARCHAR(500),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (curriculum_id) REFERENCES curriculums(id) ON DELETE CASCADE,
    FOREIGN KEY (discipline_id) REFERENCES disciplines(id) ON DELETE RESTRICT,
    UNIQUE KEY uk_curriculum_discipline (curriculum_id, discipline_id),
    CONSTRAINT chk_curriculum_activity CHECK (lecture_hours > 0 OR practical_hours > 0 OR laboratory_hours > 0)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
-- быстрый поиск по дисциплине
CREATE INDEX idx_curriculum_discipline ON curriculum_disciplines(discipline_id);

-- 18. Виды занятий
CREATE TABLE lesson_types (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL UNIQUE COMMENT 'Лекция ...',
    code VARCHAR(50) NOT NULL UNIQUE COMMENT 'lecture ...',
    duration_minutes INT UNSIGNED NOT NULL COMMENT 'длительность занятия',
    duration_per_student_minutes INT UNSIGNED NULL COMMENT 'длительность на студента (смотреть ТЗ, пункт 2 Учебное занятие)',
    description VARCHAR(500),
    allowed_room_types VARCHAR(255) COMMENT 'прописать конктретные аудитории, или оставить так',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT chk_lesson_duration CHECK (duration_minutes > 0 AND duration_minutes <= 180)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 19. Учебные поручения преподавателям
CREATE TABLE teacher_discipline_assignments (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    university_id INT UNSIGNED NOT NULL,
    teacher_id INT UNSIGNED NOT NULL,
    curriculum_discipline_id INT UNSIGNED NOT NULL COMMENT 'ссылка на дисциплину с учебного плана',
    lesson_type_id INT UNSIGNED NOT NULL COMMENT 'какой вид занятия ведет',
    assigned_hours TINYINT UNSIGNED NOT NULL COMMENT 'выделенное количество часов',
    preferred_days VARCHAR(100) COMMENT 'предпочтительные дни недели',
    preferred_time_slots VARCHAR(100) COMMENT 'предпочтительные номера пар',
    notes VARCHAR(500),
    is_confirmed BOOLEAN DEFAULT FALSE COMMENT 'подтверждено или нет',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (university_id) REFERENCES universities(id) ON DELETE CASCADE,
    FOREIGN KEY (teacher_id) REFERENCES teachers(id) ON DELETE RESTRICT,
    FOREIGN KEY (curriculum_discipline_id) REFERENCES curriculum_disciplines(id) ON DELETE CASCADE,
    FOREIGN KEY (lesson_type_id) REFERENCES lesson_types(id) ON DELETE RESTRICT,
    UNIQUE KEY uk_teacher_curriculum_lesson (teacher_id, curriculum_discipline_id, lesson_type_id),
    CONSTRAINT chk_assignment_hours CHECK (assigned_hours > 0 AND assigned_hours <= 200)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
-- для быстрого поиска по преподавателю и дисциплине
CREATE INDEX idx_assignment_teacher ON teacher_discipline_assignments(teacher_id);
CREATE INDEX idx_assignment_curriculum ON teacher_discipline_assignments(curriculum_discipline_id);

-- 20. Производственный календарь
CREATE TABLE calendar_days (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    university_id INT UNSIGNED NOT NULL,
    semester_id INT UNSIGNED NOT NULL,
    date DATE NOT NULL,
    week_number TINYINT UNSIGNED NOT NULL,
    week_parity ENUM('odd', 'even') NOT NULL COMMENT 'четность нечетность',
    day_of_week TINYINT UNSIGNED NOT NULL,
    is_working_day BOOLEAN DEFAULT TRUE,
    is_holiday BOOLEAN DEFAULT FALSE,
    holiday_name VARCHAR(255) NULL,
    notes VARCHAR(500),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (university_id) REFERENCES universities(id) ON DELETE CASCADE,
    FOREIGN KEY (semester_id) REFERENCES semesters(id) ON DELETE CASCADE,
    UNIQUE KEY uk_university_date (university_id, date),
    CONSTRAINT chk_calendar_week CHECK (week_number BETWEEN 1 AND 20),
    CONSTRAINT chk_calendar_day CHECK (day_of_week BETWEEN 1 AND 6)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
-- для поиска по семестру или неделе
CREATE INDEX idx_calendar_semester ON calendar_days(semester_id);
CREATE INDEX idx_calendar_week ON calendar_days(semester_id, week_number);

-- 21. Расписание (финальная таблица)
CREATE TABLE schedule_entries (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    university_id INT UNSIGNED NOT NULL,
    semester_id INT UNSIGNED NOT NULL,
    week_parity ENUM('odd', 'even', 'all') NOT NULL,
    day_of_week TINYINT UNSIGNED NOT NULL,
    pair_number TINYINT UNSIGNED NOT NULL,
    study_group_id INT UNSIGNED NOT NULL,
    discipline_id INT UNSIGNED NOT NULL,
    lesson_type_id INT UNSIGNED NOT NULL,
    teacher_id INT UNSIGNED NOT NULL,
    room_id INT UNSIGNED NULL,
    status ENUM('planned', 'confirmed', 'cancelled', 'rescheduled') DEFAULT 'planned',
    original_entry_id INT UNSIGNED NULL,
    notes VARCHAR(500),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (university_id) REFERENCES universities(id) ON DELETE CASCADE,
    FOREIGN KEY (semester_id) REFERENCES semesters(id) ON DELETE RESTRICT,
    FOREIGN KEY (study_group_id) REFERENCES study_groups(id) ON DELETE RESTRICT,
    FOREIGN KEY (discipline_id) REFERENCES disciplines(id) ON DELETE RESTRICT,
    FOREIGN KEY (lesson_type_id) REFERENCES lesson_types(id) ON DELETE RESTRICT,
    FOREIGN KEY (teacher_id) REFERENCES teachers(id) ON DELETE RESTRICT,
    FOREIGN KEY (room_id) REFERENCES rooms(id) ON DELETE SET NULL,
    FOREIGN KEY (original_entry_id) REFERENCES schedule_entries(id) ON DELETE SET NULL,
    UNIQUE KEY uk_teacher_time (semester_id, week_parity, day_of_week, pair_number, teacher_id),
    UNIQUE KEY uk_group_time (semester_id, week_parity, day_of_week, pair_number, study_group_id),
    UNIQUE KEY uk_room_time (semester_id, week_parity, day_of_week, pair_number, room_id),
    CONSTRAINT chk_schedule_day CHECK (day_of_week BETWEEN 1 AND 6),
    CONSTRAINT chk_schedule_pair CHECK (pair_number BETWEEN 1 AND 8)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
-- для поиска по семестру, преподавателю, учебной группе, аудитории, дисциплине
CREATE INDEX idx_schedule_semester ON schedule_entries(semester_id);
CREATE INDEX idx_schedule_teacher ON schedule_entries(teacher_id);
CREATE INDEX idx_schedule_study_group ON schedule_entries(study_group_id);
CREATE INDEX idx_schedule_room ON schedule_entries(room_id);
CREATE INDEX idx_schedule_discipline ON schedule_entries(discipline_id);
