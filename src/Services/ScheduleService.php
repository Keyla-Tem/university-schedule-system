<?php
namespace App\Services;

use App\Repositories\ScheduleRepository;
use App\Exceptions\ScheduleConflictException;

class ScheduleService {
    private $repository;

    public function __construct(ScheduleRepository $repository) {
        $this->repository = $repository;
    }

    /**
     * Правило 1: Валидация нагрузки преподавателя (не более 5 пар и 8 часов)
     */
    public function validateTeacherLoad($teacherId, $dayOfWeek, $semesterId, $lessonDurationHours) {
        $load = $this->repository->getTeacherDailyLoad($teacherId, $dayOfWeek, $semesterId);
        
        // Ограничение: не более 5 пар в день
        if (($load['count'] ?? 0) >= 5) {
            return ['allowed' => false, 'message' => 'Преподаватель уже ведет 5 пар в этот день.'];
        }

        // Ограничение: не более 8 астрономических часов в день
        if ((($load['total_hours'] ?? 0) + $lessonDurationHours) > 8) {
            return ['allowed' => false, 'message' => 'Превышен лимит рабочего времени преподавателя (8 часов).'];
        }

        return ['allowed' => true];
    }

    /**
     * Правило 2: Проверка группы на предмет пересечения занятий (Double-booking)
     * @throws ScheduleConflictException
     */
    public function checkGroupAvailability($semesterId, $dayOfWeek, $bellScheduleId, $studyGroupId, $excludeId = null) {
        // Запрашиваем репозиторий, есть ли уже у этой группы пара в это же время
        $hasConflict = $this->repository->hasGroupConflict(
            $semesterId, 
            $dayOfWeek, 
            $bellScheduleId, 
            $studyGroupId, 
            $excludeId
        );

        if ($hasConflict) {
            // Если есть накладка — выбрасываем наше кастомное исключение
            throw new ScheduleConflictException(
                "Учебная группа уже имеет занятие в этот семестр, день недели и на этой паре."
            );
        }

        return true;
    }
}