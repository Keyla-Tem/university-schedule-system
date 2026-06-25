<?php
// Вспомогательная функция безопасного вывода
if (!function_exists('e')) {
    function e($str) {
        return htmlspecialchars($str ?? '', ENT_QUOTES, 'UTF-8');
    }
}
?>

<div class="page-content">
    <h2>Аудитории университета</h2>

    <?php if (($_SESSION['role'] ?? '') === 'admin'): ?>

    <div class="crud-form-container" style="margin-bottom: 20px;">
        <h3 style="margin-bottom: 15px; font-size: 14px; font-weight: 600;">Добавить новую аудиторию</h3>
        <form action="index.php?route=classrooms" method="POST" class="crud-form">
            <input type="hidden" name="action" value="create_room">
            
            <div style="display: flex; flex-wrap: wrap; gap: 15px; align-items: flex-end;">
                
                <div class="form-group" style="flex: 1; min-width: 120px;">
                    <label style="display: block; font-size: 12px; margin-bottom: 5px;">Номер комнаты</label>
                    <input type="text" name="room_number" class="form-control" placeholder="Например, 403" required style="width: 100%;">
                </div>

                <div class="form-group" style="flex: 1; min-width: 15px;">
                    <label style="display: block; font-size: 12px; margin-bottom: 5px;">Вместимость (чел)</label>
                    <input type="number" name="capacity" class="form-control" placeholder="30" required style="width: 100%;">
                </div>

                <div class="form-group" style="flex: 1; min-width: 180px;">
                    <label style="display: block; font-size: 12px; margin-bottom: 5px;">Корпус</label>
                    <select name="building_id" class="form-control" required style="width: 100%;">
                        <option value="">-- Выберите корпус --</option>
                        <?php foreach ($buildings as $b): ?>
                            <option value="<?= $b['id'] ?>"><?= e($b['name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group" style="flex: 1; min-width: 180px;">
                    <label style="display: block; font-size: 12px; margin-bottom: 5px;">Тип аудитории</label>
                    <select name="room_type_id" class="form-control" required style="width: 100%;">
                        <option value="">-- Выберите тип --</option>
                        <?php foreach ($roomTypes as $rt): ?>
                            <option value="<?= $rt['id'] ?>"><?= e($rt['name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group" style="flex: 1; min-width: 200px;">
                    <label style="display: block; font-size: 12px; margin-bottom: 5px;">Примечание</label>
                    <input type="text" name="notes" class="form-control" placeholder="Доп. информация" style="width: 100%;">
                </div>

                <div>
                    <button type="submit" class="btn btn-primary" style="white-space: nowrap; padding: 10px 20px;">Сохранить</button>
                </div>
            </div>
        </form>
    </div>

    <?php endif; ?>

    <div class="list-grid">
        <?php foreach ($rooms as $room): ?>
            <div class="list-card-wrapper" style="position: relative; display: inline-block;">
                <a href="index.php?route=classrooms&id=<?= $room['id'] ?>" 
                   class="list-card <?= (isset($_GET['id']) && $_GET['id'] == $room['id']) ? 'active' : '' ?>"
                   style="padding-right: 40px; display: block; height: 100%;">
                    <strong><?= e($room['room_number']) ?></strong>
                    <span class="sub"><?= e($room['building_name']) ?></span>
                    <span class="badge"><?= $room['lesson_count'] ?> пар</span>
                </a>
                
                <?php if (($_SESSION['role'] ?? '') === 'admin'): ?>

                <form action="index.php?route=classrooms" method="POST" onsubmit="return confirm('Реально удалить эту аудиторию?');" style="position: absolute; top: 10px; right: 10px; z-index: 10;">
                    <input type="hidden" name="action" value="delete_room">
                    <input type="hidden" name="room_id" value="<?= $room['id'] ?>">
                    <button type="submit" class="btn btn-danger" title="Удалить" style="border: none; background: none; cursor: pointer; padding: 5px; font-weight: bold; font-size: 14px; line-height: 1;">×</button>
                </form>

                <?php endif; ?>

            </div>
        <?php endforeach; ?>
    </div>

    <?php if ($selected_room): ?>
        <h3 style="margin-top: 30px;">Расписание аудитории <?= e($selected_room['room_number']) ?> (<?= e($selected_room['building_name']) ?>)</h3>
        <p class="meta-info">Тип: <?= e($selected_room['type_name'] ?? 'Общая') ?> | Вместимость: <?= $selected_room['capacity'] ?> чел.</p>

        <?php if (empty($room_schedule)): ?>
            <div class="empty-state">В этой аудитории пока нет занятий на этот семестр</div>
        <?php else: ?>
            <table class="schedule-table">
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