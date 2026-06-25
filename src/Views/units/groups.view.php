<?php 
// Безопасное подключение хедера через относительный путь
include dirname(__DIR__) . '/layout/header.php'; 
?>

<div class="container">
    <h1>Список учебных групп</h1>

    <?php if (($_SESSION['role'] ?? 'user') === 'admin'): ?>
    <div class="crud-form-container" style="margin-bottom: 25px; padding: 15px; border: 1px solid #ddd; border-radius: 4px;">
        <form action="index.php?route=groups" method="POST" class="crud-form">
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
    <?php endif; ?>

    <table class="schedule-table">
        <thead>
            <tr>
                <th>Название</th>
                <th>Кафедра</th>
                <th>Студентов</th>
                <?php if (($_SESSION['role'] ?? 'user') === 'admin'): ?>
                    <th style="width: 50px;">Действие</th>
                <?php endif; ?>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($groups as $group): ?>
            <tr>
                <td><?= htmlspecialchars($group['name']) ?></td>
                <td><?= htmlspecialchars($group['unit_name'] ?? '—') ?></td>
                <td><?= htmlspecialchars($group['student_count']) ?></td>
                
                <?php if (($_SESSION['role'] ?? 'user') === 'admin'): ?>
                <td>
                    <form action="index.php?route=groups" method="POST" onsubmit="return confirm('Удалить группу?');">
                        <input type="hidden" name="action" value="delete_group">
                        <input type="hidden" name="group_id" value="<?= $group['id'] ?>">
                        <button type="submit" class="btn btn-danger">×</button>
                    </form>
                </td>
                <?php endif; ?>
                
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<?php 
// Безопасное подключение футера
include dirname(__DIR__) . '/layout/footer.php'; 
?>