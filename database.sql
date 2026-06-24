SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

CREATE DATABASE IF NOT EXISTS `university_schedule`
  DEFAULT CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

USE `university_schedule`;

-- ------------------------------------------------------------
-- universities
-- ------------------------------------------------------------
CREATE TABLE `universities` (
  `id`           INT            NOT NULL AUTO_INCREMENT,
  `name`         VARCHAR(255)   NOT NULL,
  `short_name`   VARCHAR(100)   DEFAULT NULL,
  `created_at`   TIMESTAMP      NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at`   TIMESTAMP      NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ------------------------------------------------------------
-- organization_units  (иерархическая, parent_id → self)
-- ------------------------------------------------------------
CREATE TABLE `organization_units` (
  `id`            INT           NOT NULL AUTO_INCREMENT,
  `university_id` INT           NOT NULL,
  `parent_id`     INT           DEFAULT NULL,
  `name`          VARCHAR(255)  NOT NULL,
  `short_name`    VARCHAR(100)  DEFAULT NULL,
  `unit_type`     ENUM('faculty','department','institute','other') DEFAULT 'other',
  `created_at`    TIMESTAMP     NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `fk_org_unit_university` (`university_id`),
  KEY `fk_org_unit_parent`     (`parent_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ------------------------------------------------------------
-- specialties
-- ------------------------------------------------------------
CREATE TABLE `specialties` (
  `id`                   INT          NOT NULL AUTO_INCREMENT,
  `university_id`        INT          NOT NULL,
  `organization_unit_id` INT          DEFAULT NULL,
  `code`                 VARCHAR(50)  DEFAULT NULL,
  `name`                 VARCHAR(255) NOT NULL,
  `created_at`           TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `fk_specialties_university` (`university_id`),
  KEY `fk_specialties_org_unit`   (`organization_unit_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ------------------------------------------------------------
-- buildings
-- ------------------------------------------------------------
CREATE TABLE `buildings` (
  `id`            INT           NOT NULL AUTO_INCREMENT,
  `university_id` INT           NOT NULL,
  `name`          VARCHAR(255)  NOT NULL,
  `address`       VARCHAR(300)  DEFAULT NULL,
  `created_at`    TIMESTAMP     NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `fk_buildings_university` (`university_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ------------------------------------------------------------
-- building_distances
-- ------------------------------------------------------------
CREATE TABLE `building_distances` (
  `id`                  INT      NOT NULL AUTO_INCREMENT,
  `university_id`       INT      NOT NULL,
  `building_from_id`    INT      NOT NULL,
  `building_to_id`      INT      NOT NULL,
  `travel_time_minutes` TINYINT  DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_bd_university`    (`university_id`),
  KEY `fk_bd_building_from` (`building_from_id`),
  KEY `fk_bd_building_to`   (`building_to_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ------------------------------------------------------------
-- room_types
-- ------------------------------------------------------------
CREATE TABLE `room_types` (
  `id`          INT          NOT NULL AUTO_INCREMENT,
  `name`        VARCHAR(100) NOT NULL,
  `description` VARCHAR(255) DEFAULT NULL,
  `created_at`  TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ------------------------------------------------------------
-- rooms
-- ------------------------------------------------------------
CREATE TABLE `rooms` (
  `id`            INT          NOT NULL AUTO_INCREMENT,
  `university_id` INT          NOT NULL,
  `building_id`   INT          NOT NULL,
  `room_type_id`  INT          DEFAULT NULL,
  `room_number`   VARCHAR(50)  NOT NULL,
  `capacity`      INT          DEFAULT NULL,
  `is_online`     TINYINT(1)   NOT NULL DEFAULT 0,
  `notes`         VARCHAR(500) DEFAULT NULL,
  `created_at`    TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `fk_rooms_university` (`university_id`),
  KEY `fk_rooms_building`   (`building_id`),
  KEY `fk_rooms_type`       (`room_type_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ------------------------------------------------------------
-- users
-- ------------------------------------------------------------
CREATE TABLE `users` (
  `id`            INT          NOT NULL AUTO_INCREMENT,
  `university_id` INT          NOT NULL,
  `name`          VARCHAR(255) NOT NULL,
  `email`         VARCHAR(255) NOT NULL,
  `password_hash` VARCHAR(255) NOT NULL,
  `full_name`     VARCHAR(255) DEFAULT NULL,
  `created_at`    TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_users_email` (`email`),
  KEY `fk_users_university` (`university_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ------------------------------------------------------------
-- teachers
-- ------------------------------------------------------------
CREATE TABLE `teachers` (
  `id`                   INT          NOT NULL AUTO_INCREMENT,
  `university_id`        INT          NOT NULL,
  `organization_unit_id` INT          DEFAULT NULL,
  `last_name`            VARCHAR(100) NOT NULL,
  `first_name`           VARCHAR(100) NOT NULL,
  `middle_name`          VARCHAR(100) DEFAULT NULL,
  `degree`               VARCHAR(100) DEFAULT NULL,
  `position`             VARCHAR(100) DEFAULT NULL,
  `email`                VARCHAR(100) DEFAULT NULL,
  `max_hours_per_day`    TINYINT      DEFAULT NULL,
  `max_pairs_per_day`    TINYINT      DEFAULT NULL,
  `max_hours_per_week`   TINYINT      DEFAULT NULL,
  `max_pairs_per_week`   TINYINT      DEFAULT NULL,
  `is_active`            TINYINT(1)   NOT NULL DEFAULT 1,
  `created_at`           TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at`           TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `fk_teachers_university` (`university_id`),
  KEY `fk_teachers_org_unit`   (`organization_unit_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ------------------------------------------------------------
-- disciplines
-- ------------------------------------------------------------
CREATE TABLE `disciplines` (
  `id`            INT          NOT NULL AUTO_INCREMENT,
  `university_id` INT          NOT NULL,
  `code`          VARCHAR(50)  DEFAULT NULL,
  `name`          VARCHAR(255) NOT NULL,
  `description`   TEXT         DEFAULT NULL,
  `created_at`    TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at`    TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `fk_disciplines_university` (`university_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ------------------------------------------------------------
-- lesson_types
-- ------------------------------------------------------------
CREATE TABLE `lesson_types` (
  `id`                           INT          NOT NULL AUTO_INCREMENT,
  `name`                         VARCHAR(100) NOT NULL,
  `description`                  VARCHAR(300) DEFAULT NULL,
  `duration_minutes`             INT          DEFAULT NULL,
  `duration_per_student_minutes` INT          DEFAULT NULL,
  `allowed_room_types`           VARCHAR(255) DEFAULT NULL,
  `created_at`                   TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ------------------------------------------------------------
-- semesters
-- ------------------------------------------------------------
CREATE TABLE `semesters` (
  `id`              INT        NOT NULL AUTO_INCREMENT,
  `university_id`   INT        NOT NULL,
  `academic_year`   INT        NOT NULL,
  `semester_number` TINYINT    NOT NULL,
  `start_date`      DATE       NOT NULL,
  `end_date`        DATE       NOT NULL,
  `total_weeks`     TINYINT    DEFAULT NULL,
  `is_active`       TINYINT(1) NOT NULL DEFAULT 0,
  `created_at`      TIMESTAMP  NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at`      TIMESTAMP  NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `fk_semesters_university` (`university_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ------------------------------------------------------------
-- academic_groups
-- ------------------------------------------------------------
CREATE TABLE `academic_groups` (
  `id`              INT          NOT NULL AUTO_INCREMENT,
  `specialty_id`    INT          NOT NULL,
  `name`            VARCHAR(100) NOT NULL,
  `year_of_study`   TINYINT      DEFAULT NULL,
  `semester_number` TINYINT      DEFAULT NULL,
  `student_count`   INT          DEFAULT NULL,
  `is_active`       TINYINT(1)   NOT NULL DEFAULT 1,
  `created_at`      TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at`      TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `fk_academic_groups_specialty` (`specialty_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ------------------------------------------------------------
-- study_groups
-- ------------------------------------------------------------
CREATE TABLE `study_groups` (
  `id`            INT          NOT NULL AUTO_INCREMENT,
  `university_id` INT          NOT NULL,
  `name`          VARCHAR(150) NOT NULL,
  `short_name`    VARCHAR(50)  DEFAULT NULL,
  `group_type`    ENUM('lecture','practice','lab','other') DEFAULT 'other',
  `student_count` INT          DEFAULT NULL,
  `is_active`     TINYINT(1)   NOT NULL DEFAULT 1,
  `created_at`    TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at`    TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `fk_study_groups_university` (`university_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ------------------------------------------------------------
-- study_group_compositions
-- ------------------------------------------------------------
CREATE TABLE `study_group_compositions` (
  `id`               INT     NOT NULL AUTO_INCREMENT,
  `study_group_id`   INT     NOT NULL,
  `academic_group_id` INT    NOT NULL,
  `student_portion`  TINYINT DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_sgc_study_group`    (`study_group_id`),
  KEY `fk_sgc_academic_group` (`academic_group_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ------------------------------------------------------------
-- curriculums
-- ------------------------------------------------------------
CREATE TABLE `curriculums` (
  `id`                  INT          NOT NULL AUTO_INCREMENT,
  `university_id`       INT          NOT NULL,
  `specialty_id`        INT          DEFAULT NULL,
  `academic_year_start` YEAR         NOT NULL,
  `academic_year_end`   YEAR         NOT NULL,
  `semester_number`     TINYINT      DEFAULT NULL,
  `semester_weeks`      TINYINT      DEFAULT NULL,
  `description`         VARCHAR(300) DEFAULT NULL,
  `is_active`           TINYINT(1)   NOT NULL DEFAULT 1,
  `created_at`          TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at`          TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `fk_curriculums_university` (`university_id`),
  KEY `fk_curriculums_specialty`  (`specialty_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ------------------------------------------------------------
-- curriculum_disciplines
-- FIX #2: independent_work_hours TINYINT → SMALLINT
--         (TINYINT UNSIGNED max=255, реальные учебные планы легко превышают)
-- ------------------------------------------------------------
CREATE TABLE `curriculum_disciplines` (
  `id`                     INT        NOT NULL AUTO_INCREMENT,
  `curriculum_id`          INT        NOT NULL,
  `discipline_id`          INT        NOT NULL,
  `lecture_hours`          TINYINT    DEFAULT NULL,
  `practical_hours`        TINYINT    DEFAULT NULL,
  `laboratory_hours`       TINYINT    DEFAULT NULL,
  `independent_work_hours` SMALLINT   DEFAULT NULL,  -- FIX #2
  `assessment_type`        ENUM('exam','credit','differentiated_credit','coursework','other') DEFAULT 'exam',
  `notes`                  VARCHAR(300) DEFAULT NULL,
  `created_at`             TIMESTAMP  NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at`             TIMESTAMP  NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `fk_cd_curriculum` (`curriculum_id`),
  KEY `fk_cd_discipline` (`discipline_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ------------------------------------------------------------
-- teacher_discipline_assignments
-- ------------------------------------------------------------
CREATE TABLE `teacher_discipline_assignments` (
  `id`                       INT          NOT NULL AUTO_INCREMENT,
  `university_id`            INT          NOT NULL,
  `teacher_id`               INT          NOT NULL,
  `curriculum_discipline_id` INT          NOT NULL,
  `lesson_type_id`           INT          NOT NULL,
  `assigned_hours`           TINYINT      DEFAULT NULL,
  `preferred_days`           VARCHAR(100) DEFAULT NULL,
  `preferred_time_slots`     VARCHAR(100) DEFAULT NULL,
  `notes`                    VARCHAR(300) DEFAULT NULL,
  `is_confirmed`             TINYINT(1)   NOT NULL DEFAULT 0,
  `created_at`               TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at`               TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `fk_tda_university`            (`university_id`),
  KEY `fk_tda_teacher`               (`teacher_id`),
  KEY `fk_tda_curriculum_discipline` (`curriculum_discipline_id`),
  KEY `fk_tda_lesson_type`           (`lesson_type_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ------------------------------------------------------------
-- calendar_days
-- ------------------------------------------------------------
CREATE TABLE `calendar_days` (
  `id`             INT          NOT NULL AUTO_INCREMENT,
  `semester_id`    INT          NOT NULL,
  `date`           DATE         NOT NULL,
  `week_number`    TINYINT      DEFAULT NULL,
  `week_parity`    ENUM('odd','even','both') DEFAULT 'both',
  `day_of_week`    TINYINT      NOT NULL COMMENT '1=Mon … 7=Sun',
  `is_working_day` TINYINT(1)   NOT NULL DEFAULT 1,
  `is_holiday`     TINYINT(1)   NOT NULL DEFAULT 0,
  `holiday_name`   VARCHAR(255) DEFAULT NULL,
  `notes`          VARCHAR(300) DEFAULT NULL,
  `created_at`     TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at`     TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `fk_calendar_days_semester` (`semester_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ------------------------------------------------------------
-- bell_schedules
-- ------------------------------------------------------------
CREATE TABLE `bell_schedules` (
  `id`            INT       NOT NULL AUTO_INCREMENT,
  `university_id` INT       NOT NULL,
  `pair_number`   INT       NOT NULL,
  `start_time`    TIME      NOT NULL,
  `end_time`      TIME      NOT NULL,
  `created_at`    TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `fk_bell_university` (`university_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ------------------------------------------------------------
-- schedule_entries
-- FIX #1: добавлена колонка bell_schedule_id INT NOT NULL
-- FIX #4: два составных индекса для проверки конфликтов
-- ------------------------------------------------------------
CREATE TABLE `schedule_entries` (
  `id`                INT         NOT NULL AUTO_INCREMENT,
  `university_id`     INT         NOT NULL,
  `semester_id`       INT         NOT NULL,
  `week_number`       TINYINT     DEFAULT NULL,
  `week_parity`       ENUM('odd','even','both') DEFAULT 'both',
  `day_of_week`       TINYINT     NOT NULL COMMENT '1=Mon … 7=Sun',
  `bell_schedule_id`  INT         NOT NULL,               -- FIX #1
  `study_group_id`    INT         NOT NULL,
  `discipline_id`     INT         NOT NULL,
  `lesson_type_id`    INT         NOT NULL,
  `teacher_id`        INT         NOT NULL,
  `room_id`           INT         DEFAULT NULL,
  `original_entry_id` INT         DEFAULT NULL COMMENT 'для замен/переносов',
  `notes`             VARCHAR(255) DEFAULT NULL,
  `status`            ENUM('active','cancelled','moved','replaced') DEFAULT 'active',
  `created_at`        TIMESTAMP   NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at`        TIMESTAMP   NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  -- одиночные индексы для FK
  KEY `fk_se_university`    (`university_id`),
  KEY `fk_se_semester`      (`semester_id`),
  KEY `fk_se_bell_schedule` (`bell_schedule_id`),
  KEY `fk_se_study_group`   (`study_group_id`),
  KEY `fk_se_discipline`    (`discipline_id`),
  KEY `fk_se_lesson_type`   (`lesson_type_id`),
  KEY `fk_se_teacher`       (`teacher_id`),
  KEY `fk_se_room`          (`room_id`),
  KEY `fk_se_original`      (`original_entry_id`),
  -- FIX #4: составные индексы для проверки конфликтов
  KEY `idx_conflict_teacher`
      (`semester_id`, `week_parity`, `day_of_week`, `bell_schedule_id`, `teacher_id`),
  KEY `idx_conflict_room`
      (`semester_id`, `week_parity`, `day_of_week`, `bell_schedule_id`, `room_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============================================================
-- FOREIGN KEYS
-- FIX #3: ON DELETE CASCADE для логических parent-child связей
-- ============================================================

-- organization_units → universities (CASCADE: нет универа — нет подразделений)
ALTER TABLE `organization_units`
  ADD CONSTRAINT `fk_org_unit_university`
    FOREIGN KEY (`university_id`) REFERENCES `universities` (`id`)
    ON DELETE CASCADE,
  ADD CONSTRAINT `fk_org_unit_parent`
    FOREIGN KEY (`parent_id`) REFERENCES `organization_units` (`id`)
    ON DELETE CASCADE;

-- specialties → universities / organization_units
ALTER TABLE `specialties`
  ADD CONSTRAINT `fk_specialties_university`
    FOREIGN KEY (`university_id`) REFERENCES `universities` (`id`)
    ON DELETE CASCADE,
  ADD CONSTRAINT `fk_specialties_org_unit`
    FOREIGN KEY (`organization_unit_id`) REFERENCES `organization_units` (`id`)
    ON DELETE SET NULL;   -- кафедру удалили — специальность остаётся, просто без привязки

-- buildings → universities
ALTER TABLE `buildings`
  ADD CONSTRAINT `fk_buildings_university`
    FOREIGN KEY (`university_id`) REFERENCES `universities` (`id`)
    ON DELETE CASCADE;

-- building_distances → universities / buildings
ALTER TABLE `building_distances`
  ADD CONSTRAINT `fk_bd_university`
    FOREIGN KEY (`university_id`) REFERENCES `universities` (`id`)
    ON DELETE CASCADE,
  ADD CONSTRAINT `fk_bd_building_from`
    FOREIGN KEY (`building_from_id`) REFERENCES `buildings` (`id`)
    ON DELETE CASCADE,
  ADD CONSTRAINT `fk_bd_building_to`
    FOREIGN KEY (`building_to_id`) REFERENCES `buildings` (`id`)
    ON DELETE CASCADE;

-- rooms → universities / buildings / room_types
ALTER TABLE `rooms`
  ADD CONSTRAINT `fk_rooms_university`
    FOREIGN KEY (`university_id`) REFERENCES `universities` (`id`)
    ON DELETE CASCADE,
  ADD CONSTRAINT `fk_rooms_building`
    FOREIGN KEY (`building_id`) REFERENCES `buildings` (`id`)
    ON DELETE CASCADE,
  ADD CONSTRAINT `fk_rooms_type`
    FOREIGN KEY (`room_type_id`) REFERENCES `room_types` (`id`)
    ON DELETE SET NULL;   -- тип удалён — аудитория остаётся

-- users → universities
ALTER TABLE `users`
  ADD CONSTRAINT `fk_users_university`
    FOREIGN KEY (`university_id`) REFERENCES `universities` (`id`)
    ON DELETE CASCADE;

-- teachers → universities / organization_units
ALTER TABLE `teachers`
  ADD CONSTRAINT `fk_teachers_university`
    FOREIGN KEY (`university_id`) REFERENCES `universities` (`id`)
    ON DELETE CASCADE,
  ADD CONSTRAINT `fk_teachers_org_unit`
    FOREIGN KEY (`organization_unit_id`) REFERENCES `organization_units` (`id`)
    ON DELETE SET NULL;

-- disciplines → universities
ALTER TABLE `disciplines`
  ADD CONSTRAINT `fk_disciplines_university`
    FOREIGN KEY (`university_id`) REFERENCES `universities` (`id`)
    ON DELETE CASCADE;

-- semesters → universities
ALTER TABLE `semesters`
  ADD CONSTRAINT `fk_semesters_university`
    FOREIGN KEY (`university_id`) REFERENCES `universities` (`id`)
    ON DELETE CASCADE;

-- academic_groups → specialties
ALTER TABLE `academic_groups`
  ADD CONSTRAINT `fk_academic_groups_specialty`
    FOREIGN KEY (`specialty_id`) REFERENCES `specialties` (`id`)
    ON DELETE CASCADE;

-- study_groups → universities
ALTER TABLE `study_groups`
  ADD CONSTRAINT `fk_study_groups_university`
    FOREIGN KEY (`university_id`) REFERENCES `universities` (`id`)
    ON DELETE CASCADE;

-- study_group_compositions → study_groups / academic_groups
ALTER TABLE `study_group_compositions`
  ADD CONSTRAINT `fk_sgc_study_group`
    FOREIGN KEY (`study_group_id`) REFERENCES `study_groups` (`id`)
    ON DELETE CASCADE,
  ADD CONSTRAINT `fk_sgc_academic_group`
    FOREIGN KEY (`academic_group_id`) REFERENCES `academic_groups` (`id`)
    ON DELETE CASCADE;

-- curriculums → universities / specialties
ALTER TABLE `curriculums`
  ADD CONSTRAINT `fk_curriculums_university`
    FOREIGN KEY (`university_id`) REFERENCES `universities` (`id`)
    ON DELETE CASCADE,
  ADD CONSTRAINT `fk_curriculums_specialty`
    FOREIGN KEY (`specialty_id`) REFERENCES `specialties` (`id`)
    ON DELETE SET NULL;

-- curriculum_disciplines → curriculums / disciplines
ALTER TABLE `curriculum_disciplines`
  ADD CONSTRAINT `fk_cd_curriculum`
    FOREIGN KEY (`curriculum_id`) REFERENCES `curriculums` (`id`)
    ON DELETE CASCADE,
  ADD CONSTRAINT `fk_cd_discipline`
    FOREIGN KEY (`discipline_id`) REFERENCES `disciplines` (`id`)
    ON DELETE RESTRICT;   -- нельзя удалить дисциплину, если она в учплане

-- teacher_discipline_assignments → universities / teachers / curriculum_disciplines / lesson_types
ALTER TABLE `teacher_discipline_assignments`
  ADD CONSTRAINT `fk_tda_university`
    FOREIGN KEY (`university_id`) REFERENCES `universities` (`id`)
    ON DELETE CASCADE,
  ADD CONSTRAINT `fk_tda_teacher`
    FOREIGN KEY (`teacher_id`) REFERENCES `teachers` (`id`)
    ON DELETE CASCADE,
  ADD CONSTRAINT `fk_tda_curriculum_discipline`
    FOREIGN KEY (`curriculum_discipline_id`) REFERENCES `curriculum_disciplines` (`id`)
    ON DELETE CASCADE,
  ADD CONSTRAINT `fk_tda_lesson_type`
    FOREIGN KEY (`lesson_type_id`) REFERENCES `lesson_types` (`id`)
    ON DELETE RESTRICT;

-- calendar_days → semesters (CASCADE: семестр удалён — его дни удаляются)
ALTER TABLE `calendar_days`
  ADD CONSTRAINT `fk_calendar_days_semester`
    FOREIGN KEY (`semester_id`) REFERENCES `semesters` (`id`)
    ON DELETE CASCADE;

-- bell_schedules → universities
ALTER TABLE `bell_schedules`
  ADD CONSTRAINT `fk_bell_university`
    FOREIGN KEY (`university_id`) REFERENCES `universities` (`id`)
    ON DELETE CASCADE;

-- schedule_entries → все зависимости
ALTER TABLE `schedule_entries`
  ADD CONSTRAINT `fk_se_university`
    FOREIGN KEY (`university_id`) REFERENCES `universities` (`id`)
    ON DELETE CASCADE,
  ADD CONSTRAINT `fk_se_semester`
    FOREIGN KEY (`semester_id`) REFERENCES `semesters` (`id`)
    ON DELETE CASCADE,    -- семестр удалён — его расписание удаляется
  ADD CONSTRAINT `fk_se_bell_schedule`
    FOREIGN KEY (`bell_schedule_id`) REFERENCES `bell_schedules` (`id`)
    ON DELETE RESTRICT,   -- нельзя удалить пару из звонков, если она в расписании
  ADD CONSTRAINT `fk_se_study_group`
    FOREIGN KEY (`study_group_id`) REFERENCES `study_groups` (`id`)
    ON DELETE CASCADE,
  ADD CONSTRAINT `fk_se_discipline`
    FOREIGN KEY (`discipline_id`) REFERENCES `disciplines` (`id`)
    ON DELETE RESTRICT,
  ADD CONSTRAINT `fk_se_lesson_type`
    FOREIGN KEY (`lesson_type_id`) REFERENCES `lesson_types` (`id`)
    ON DELETE RESTRICT,
  ADD CONSTRAINT `fk_se_teacher`
    FOREIGN KEY (`teacher_id`) REFERENCES `teachers` (`id`)
    ON DELETE RESTRICT,   -- нельзя удалить преподавателя с активным расписанием
  ADD CONSTRAINT `fk_se_room`
    FOREIGN KEY (`room_id`) REFERENCES `rooms` (`id`)
    ON DELETE SET NULL,   -- аудитория удалена — запись остаётся (онлайн/без аудитории)
  ADD CONSTRAINT `fk_se_original`
    FOREIGN KEY (`original_entry_id`) REFERENCES `schedule_entries` (`id`)
    ON DELETE SET NULL;

SET FOREIGN_KEY_CHECKS = 1;
