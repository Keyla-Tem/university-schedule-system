<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Структура ВУЗа</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container mt-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Подразделения: <?= htmlspecialchars($university['name']) ?></h2>
        <div>
            <a href="index.php?route=universities" class="btn btn-secondary">Назад к ВУЗам</a>
            <a href="index.php?route=units/create&university_id=<?= $university['id'] ?>" class="btn btn-success">Добавить подразделение</a>
        </div>
    </div>
    <table class="table table-bordered table-striped bg-white shadow-sm">
        <thead>
            <tr><th>Название</th><th>Кратко</th><th>Тип</th><th>Родительская структура</th><th>Действия</th></tr>
        </thead>
        <tbody>
            <?php foreach ($units as $u): ?>
                <tr>
                    <td><?= htmlspecialchars($u['name']) ?></td>
                    <td><?= htmlspecialchars($u['short_name'] ?? '—') ?></td>
                    <td><span class="badge bg-info text-dark"><?= htmlspecialchars($u['unit_type']) ?></span></td>
                    <td><?= htmlspecialchars($u['parent_name'] ?? '— (Корневое)') ?></td>
                    <td>
                        <a href="index.php?route=units/edit&id=<?= $u['id'] ?>" class="btn btn-sm btn-warning">Ред.</a>
                        <a href="index.php?route=units/delete&id=<?= $u['id'] ?>&university_id=<?= $university['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Удалить?')">Удал.</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
</body>
</html>