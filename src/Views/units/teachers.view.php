<?php include $_SERVER['DOCUMENT_ROOT'] . '/keyschedule/src/Views/layout/header.php'; ?>
<div class="container">
    <h1>Список преподавателей</h1>

    <!-- ФОРМА ДОБАВЛЕНИЯ ПРЕПОДАВАТЕЛЯ (Универсальные классы для CSS-помощницы) -->
    <div class="crud-form-container" style="margin-bottom: 25px; padding: 15px; border: 1px solid #ddd; border-radius: 4px;">
        <h3 style="margin-top: 0; margin-bottom: 15px; font-size: 14px; font-weight: 600;">Добавить нового преподавателя</h3>
        <form action="/keyschedule/public/index.php?route=teachers" method="POST" class="crud-form">
            <input type="hidden" name="action" value="create_teacher">
            
            <div style="display: flex; flex-wrap: wrap; gap: 15px; align-items: flex-end;">
                
                <div class="form-group" style="flex: 1; min-width: 150px;">
                    <label style="display: block; font-size: 12px; margin-bottom: 5px;">Фамилия</label>
                    <input type="text" name="last_name" class="form-control" placeholder="Иванов" required style="width: 100%;">
                </div>

                <div class="form-group" style="flex: 1; min-width: 120px;">
                    <label style="display: block; font-size: 12px; margin-bottom: 5px;">Имя</label>
                    <input type="text" name="first_name" class="form-control" placeholder="Иван" required style="width: 100%;">
                </div>

                <div class="form-group" style="flex: 1; min-width: 120px;">
                    <label style="display: block; font-size: 12px; margin-bottom: 5px;">Отчество</label>
                    <input type="text" name="middle_name" class="form-control" placeholder="Иванович" style="width: 100%;">
                </div>

                <div class="form-group" style="flex: 1; min-width: 180px;">
                    <label style="display: block; font-size: 12px; margin-bottom: 5px;">Кафедра</label>
                    <select name="organization_unit_id" class="form-control" required style="width: 100%;">
                        <option value="">-- Выберите кафедру --</option>
                        <?php foreach ($departments as $dept): ?>
                            <option value="<?= $dept['id'] ?>"><?= htmlspecialchars($dept['name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group" style="flex: 1; min-width: 120px;">
                    <label style="display: block; font-size: 12px; margin-bottom: 5px;">Должность</label>
                    <input type="text" name="position" class="form-control" placeholder="Доцент" style="width: 100%;">
                </div>

                <div class="form-group" style="flex: 1; min-width: 100px;">
                    <label style="display: block; font-size: 12px; margin-bottom: 5px;">Уч. степень</label>
                    <input type="text" name="degree" class="form-control" placeholder="К.т.н." style="width: 100%;">
                </div>

                <div class="form-group" style="flex: 1; min-width: 150px;">
                    <label style="display: block; font-size: 12px; margin-bottom: 5px;">Email</label>
                    <input type="email" name="email" class="form-control" placeholder="example@univ.ru" style="width: 100%;">
                </div>

                <div>
                    <button type="submit" class="btn btn-primary" style="white-space: nowrap; padding: 10px 20px;">Сохранить</button>
                </div>
            </div>
        </form>
    </div>

    <!-- ТАБЛИЦА ПРЕПОДАВАТЕЛЕЙ -->
    <table class="schedule-table">
        <thead>
            <tr>
                <th>ФИО Преподавателя</th>
                <th>Кафедра</th>
                <th>Должность / Степень</th>
                <th style="width: 80px; text-align: center;">Действия</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($teachers as $teacher): ?>
            <tr>
                <td><?= htmlspecialchars($teacher['last_name'] . ' ' . $teacher['first_name'] . ' ' . $teacher['middle_name']) ?></td>
                <td><?= htmlspecialchars($teacher['department_name'] ?? 'Не указана') ?></td>
                <td>
                    <?= htmlspecialchars($teacher['position']) ?> 
                    <?= !empty($teacher['degree']) && $teacher['degree'] !== 'Нет' ? '('.htmlspecialchars($teacher['degree']).')' : '' ?>
                </td>
                <td style="text-align: center;">
                    <form action="/keyschedule/public/index.php?route=teachers" method="POST" onsubmit="return confirm('Реально удалить препода из базы?');" style="display: inline;">
                        <input type="hidden" name="action" value="delete_teacher">
                        <input type="hidden" name="teacher_id" value="<?= $teacher['id'] ?>">
                        <button type="submit" class="btn btn-danger" title="Удалить" style="border: none; background: none; cursor: pointer; color: red; font-weight: bold; font-size: 16px;">×</button>
                    </form>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
<?php include $_SERVER['DOCUMENT_ROOT'] . '/keyschedule/src/Views/layout/footer.php'; ?>