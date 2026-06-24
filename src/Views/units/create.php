<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Добавить подразделение</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container mt-5" style="max-width: 600px;">
    <div class="card shadow">
        <div class="card-header bg-success text-white"><h5>Новое подразделение для ВУЗа</h5></div>
        <div class="card-body">
            <form action="index.php?route=units/store" method="POST">
                <input type="hidden" name="university_id" value="<?= $university['id'] ?>">
                
                <div class="mb-3"><label class="form-label">Наименование</label><input type="text" name="name" class="form-control" required></div>
                <div class="mb-3"><label class="form-label">Краткое наименование</label><input type="text" name="short_name" class="form-control"></div>
                
                <div class="mb-3">
                    <label class="form-label">Тип</label>
                    <select name="unit_type" class="form-select" required>
                        <option value="faculty">Faculty / School</option>
                        <option value="department">Department (Кафедра)</option>
                        <option value="institute">Institute</option>
                        <option value="other">Other</option>
                    </select>
                </div>

                <div class="mb-3">
                    <label class="form-label">Вышестоящее подразделение (Иерархия)</label>
                    <select name="parent_id" class="form-select">
                        <option value="">Нет (Верхний уровень)</option>
                        <?php foreach ($existingUnits as $eu): ?>
                            <option value="<?= $eu['id'] ?>"><?= htmlspecialchars($eu['name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <button type="submit" class="btn btn-success">Сохранить</button>
                <a href="index.php?route=units&university_id=<?= $university['id'] ?>" class="btn btn-secondary">Отмена</a>
            </form>
        </div>
    </div>
</div>
</body>
</html>