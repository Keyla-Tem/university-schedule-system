<h3>Настройки профиля</h3>

<?php if (isset($_GET['status'])): ?>
    <div style="color: green; margin-bottom: 15px;">✅ Профиль успешно обновлен!</div>
<?php endif; ?>

<style>
    .settings-form { max-width: 400px; display: flex; flex-direction: column; gap: 15px; }
    .form-group { display: flex; flex-direction: column; gap: 5px; }
    .password-wrapper { display: flex; gap: 5px; }
</style>

<div class="settings-form">
    <form action="index.php?route=settings&action=update" method="POST">
        
        <div class="form-group">
            <label>Имя:</label>
            <input type="text" name="name" value="<?= htmlspecialchars($user['name'] ?? '') ?>" required>
        </div>

        <div class="form-group">
            <label>Email:</label>
            <input type="email" name="email" value="<?= htmlspecialchars($user['email'] ?? '') ?>" required>
        </div>

        <div class="form-group">
            <label>Университет:</label>
            <select name="university_id">
                <?php foreach ($universities as $u): ?>
                    <option value="<?= $u['id'] ?>" <?= (isset($user['university_id']) && $user['university_id'] == $u['id']) ? 'selected' : '' ?>>
                        <?= htmlspecialchars($u['name']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="form-group">
            <label>Группа:</label>
            <select name="group_id">
                <option value="">Выберите группу</option>
                <?php foreach ($groups as $g): ?>
                    <option value="<?= $g['id'] ?>" <?= (isset($user['study_group_id']) && $user['study_group_id'] == $g['id']) ? 'selected' : '' ?>>
                        <?= htmlspecialchars($g['name']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="form-group">
            <label>Новый пароль (оставь пустым, если не меняешь):</label>
            <div class="password-wrapper">
                <input type="password" id="passwordField" name="password" placeholder="••••••••">
                <button type="button" onclick="togglePass()">👁️</button>
            </div>
        </div>

        <button type="submit" style="margin-top: 10px; padding: 8px;">Сохранить</button>
    </form>
</div>

<script>
function togglePass() {
    const field = document.getElementById('passwordField');
    field.type = (field.type === 'password') ? 'text' : 'password';
}
</script>