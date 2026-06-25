<?php include $_SERVER['DOCUMENT_ROOT'] . '/keyschedule/src/Views/layout/header.php'; ?>
<div class="container">
    <h1>Список учебных групп</h1>

    <div class="crud-form-container" style="margin-bottom: 25px; padding: 15px; border: 1px solid #ddd; border-radius: 4px;">
        <form action="/keyschedule/public/index.php?route=groups" method="POST" class="crud-form">
            <input type="hidden" name="action" value="create_group">
            <div style="display: flex; gap: 15px; align-items: flex-end;">
                <div class="form-group">
                    <label>Название</label>
                    <input type="text" name="name" class="form-control" required placeholder="Б0000">
                </div>
                <div class="form-group">
                    <label>Кафедра</label>
                    <select name="organization_unit_id" class="form-control" required>
                        <?php foreach ($units as $u): ?>
                            <option value="<?= $u['id'] ?>"><?= htmlspecialchars($u['name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label>Студентов</label>
                    <input type="number" name="student_count" class="form-control" value="25">
                </div>
                <button type="submit" class="btn btn-primary">Добавить</button>
            </div>
        </form>
    </div>

    <table class="schedule-table">
        <thead>
            <tr>
                <th>Название</th>
                <th>Кафедра</th>
                <th>Студентов</th>
                <th style="width: 50px;"></th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($groups as $group): ?>
            <tr>
                <td><?= htmlspecialchars($group['name']) ?></td>
                <td><?= htmlspecialchars($group['unit_name'] ?? '—') ?></td>
                <td><?= htmlspecialchars($group['student_count']) ?></td>
                <td>
                    <form action="/keyschedule/public/index.php?route=groups" method="POST" onsubmit="return confirm('Удалить группу?');">
                        <input type="hidden" name="action" value="delete_group">
                        <input type="hidden" name="group_id" value="<?= $group['id'] ?>">
                        <button type="submit" class="btn btn-danger">×</button>
                    </form>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
<?php include $_SERVER['DOCUMENT_ROOT'] . '/keyschedule/src/Views/layout/footer.php'; ?>