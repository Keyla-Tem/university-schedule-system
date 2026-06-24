<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Добавить ВУЗ</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container mt-5" style="max-width: 500px;">
    <div class="card shadow">
        <div class="card-header bg-primary text-white"><h5>Новый Университет</h5></div>
        <div class="card-body">
            <form action="index.php?route=universities/store" method="POST">
                <div class="mb-3"><label class="form-label">Название</label><input type="text" name="name" class="form-control" required></div>
                <div class="mb-3"><label class="form-label">Краткое имя</label><input type="text" name="short_name" class="form-control"></div>
                <button type="submit" class="btn btn-success">Сохранить</button>
                <a href="index.php?route=universities" class="btn btn-secondary">Отмена</a>
            </form>
        </div>
    </div>
</div>
</body>
</html>