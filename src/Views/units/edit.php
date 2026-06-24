<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Редактировать подразделение</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container mt-5" style="max-width: 600px;">
    <div class="card shadow">
        <div class="card-header bg-warning"><h5>Редактировать подразделение</h5></div>
        <div class="card-body">
            <form action="index.php?route=units/update" method="POST">
                <input type="hidden" name="id" value="<?= $unit['id'] ?>">
                <input type="hidden" name="university_id" value="<?= $unit['university_id'] ?>">
                
                <div class="mb-3"><label class="form-label">Наименование</label><input type="text" name="name" class="form-control" value="<?= htmlspecialchars($unit['name']) ?>" required></div>
                <div class="mb-3"><label class="form-label">Краткое наименование</label><input type="text" name="short_name" class="form-control" value="<?= htmlspecialchars($unit['short_name'] ?? '') ?>"></div>
                
                <div class="mb-3">
                    <label class="form-label">Тип</label>
                    <select name="unit_type" class="form-select" required>
                        <option value="faculty" <?= $unit['unit_type'] === 'faculty' ? 'selected' : '' ?>>Faculty / School</option>
                        <option value="department" <?= $unit['unit_type'] === 'department' ? 'selected' : '' ?>>Department (Кафедра)</option>
                        <option value="institute" <?= $unit['unit_type'] === 'institute' ? 'selected' : '' ?>>Institute</option>
                        <option value="other" <?= $unit['unit_type'] === 'other' ? 'selected' : '' ?>>Other</option>
                    </select>
                </div>

                <div class="mb-3">
                    <label class="form-label">Вышестоящее подразделение</label>
                    <select name="parent_id" class="form-select">
                        <option value="">Нет (Верхний уровень)</option>
                        <?php foreach ($existingUnits as $eu): ?>
                            <?php if ($eu['id'] == $unit['id']) continue; ?>
                            <option value="<?= $eu['id'] ?>" <?= $eu['id'] == $unit['parent_id'] ? 'selected' : '' ?>><?= htmlspecialchars($eu['name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <button type="submit" class="btn btn-primary">Обновить</button>
                <a href="index.php?route=units&university_id=<?= $unit['university_id'] ?>" class="btn btn-secondary">Отмена</a>
            </form>
        </div>
    </div>
</div>
</body>
</html>