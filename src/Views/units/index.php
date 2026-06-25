<?php 
require_once dirname(__DIR__) . '/layout/header.php'; 
?>

<div class="container mt-5">
    <div class="header" style="margin-bottom: 20px;">
        <h2>Подразделения: <?= htmlspecialchars($university['name']) ?></h2>
        <a href="?route=universities" class="btn" style="background: var(--primary); color: white; padding: 5px 15px; border-radius: var(--radius);">Назад к ВУЗам</a>
    </div>

    <table class="schedule-table">
        <thead>
            <tr>
                <th>Название</th>
                <th>Кратко</th>
                <th>Тип</th>
                <th>Родительская структура</th>
                <th>Действия</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($units)): ?>
                <?php foreach ($units as $u): ?>
                    <tr>
                        <td><?= htmlspecialchars($u['name']) ?></td>
                        <td><?= htmlspecialchars($u['short_name'] ?? '—') ?></td>
                        <td><span class="badge"><?= htmlspecialchars($u['unit_type'] ?? 'Школа') ?></span></td>
                        <td><?= htmlspecialchars($u['parent_name'] ?? '— (Корневое)') ?></td>
                        <td>
                            <a href="?route=units/edit&id=<?= $u['id'] ?>" style="margin-right: 10px;">✏️</a>
                            <a href="?route=units/delete&id=<?= $u['id'] ?>&university_id=<?= $university['id'] ?>" onclick="return confirm('Удалить?')">❌</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="5" style="text-align: center;">Подразделения не найдены</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<?php 
require_once dirname(__DIR__) . '/layout/footer.php'; 
?>