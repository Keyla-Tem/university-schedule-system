<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Список Университетов</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container mt-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Справочник Университетов</h2>
        <a href="index.php?route=universities/create" class="btn btn-primary">Добавить ВУЗ</a>
    </div>
    <div class="row">
        <?php foreach ($universities as $uni): ?>
            <div class="col-md-4 mb-3">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <h5 class="card-title"><?= htmlspecialchars($uni['name']) ?></h5>
                        <p class="text-muted">Код: <?= htmlspecialchars($uni['short_name'] ?? '—') ?></p>
                        <div class="d-flex justify-content-between mt-3">
                            <a href="index.php?route=units&university_id=<?= $uni['id'] ?>" class="btn btn-sm btn-success">Структура</a>
                            <div>
                                <a href="index.php?route=universities/edit&id=<?= $uni['id'] ?>" class="btn btn-sm btn-outline-warning">Ред.</a>
                                <a href="index.php?route=universities/delete&id=<?= $uni['id'] ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Удалить ВУЗ?')">Удал.</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>
</body>
</html>