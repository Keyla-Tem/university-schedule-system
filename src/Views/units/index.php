<?php 
require_once dirname(__DIR__) . '/layout/header.php'; 
?>

<div class="container mt-5">
    <div class="header">
        <h2>Подразделения: <?= htmlspecialchars($university['name']) ?></h2>
        <a href="?route=universities" class="btn">Назад</a>
    </div>

    <thead>
        <tr><th>Название</th><th>Кратко</th><th>Тип</th><th>Родительская структура</th><th>Действия</th></tr>
    </thead>
    <tbody>
        <?php foreach ($units as $u): ?>
            <tr>
                <td><?= htmlspecialchars($u['name']) ?></td>
                <td><?= htmlspecialchars($u['short_name'] ?? '—') ?></td>
                <td><span class="badge"><?= htmlspecialchars($u['unit_type']) ?></span></td>
                <td><?= htmlspecialchars($u['parent_name'] ?? '—') ?></td>
                <td>
                    <a href="?route=units/edit&id=<?= $u['id'] ?>">Ред.</a>
                    <a href="?route=units/delete&id=<?= $u['id'] ?>" onclick="return confirm('Удалить?')">Удал.</a>
                </td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</div>

<?php 
require_once dirname(__DIR__) . '/layout/footer.php'; 
?>