<?php 
require_once dirname(__DIR__) . '/layout/header.php'; 
?>

<div class="container" style="max-width: 600px; margin-top: 40px;">
    <div class="header">
        <h2>Добавление университета</h2>
    </div>

    <div class="form-wrapper" style="background: #fff; padding: 20px; border-radius: var(--radius); border: 1px solid var(--border-color);">
        <form action="index.php?route=universities/store" method="POST">
            <div style="margin-bottom: 15px;">
                <label>Название</label>
                <input type="text" name="name" class="form-control" required style="width: 100%; padding: 8px;">
            </div>
            <div style="margin-bottom: 15px;">
                <label>Краткое имя</label>
                <input type="text" name="short_name" class="form-control" style="width: 100%; padding: 8px;">
            </div>
            
            <div style="margin-top: 20px;">
                <button type="submit" class="btn" style="background: var(--primary); color: #fff;">Сохранить</button>
                <a href="?route=universities" class="btn" style="background: #ccc;">Отмена</a>
            </div>
        </form>
    </div>
</div>

<?php 
require_once dirname(__DIR__) . '/layout/footer.php'; 
?>