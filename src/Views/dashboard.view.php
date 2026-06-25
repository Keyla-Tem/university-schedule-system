<style>
    /* Основной контейнер */
    .dashboard-container { display: flex; flex-direction: column; gap: 30px; padding: 20px; }
    
    /* Сетка для 4 карточек */
    .dashboard-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 20px; }
    
    /* Сетка для таблиц/списков */
    .dashboard-info { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; }
    
    /* Карточка */
    .stat-card { background: #fff; padding: 20px; border-radius: 10px; box-shadow: 0 2px 5px rgba(0,0,0,0.1); }
    .stat-card h3 { margin: 0; color: #6366f1; font-size: 24px; text-align: center; }
    .stat-card p { margin: 5px 0 0; color: #64748b; text-align: center; }
    
    @media (max-width: 768px) {
        .dashboard-info { grid-template-columns: 1fr; }
    }
</style>

<div class="dashboard-container">
    <h2>Общая сводка</h2>

    <!-- Блок 1: 4 счетчика -->
    <div class="dashboard-grid">
        <div class="stat-card">
            <h3><?= $stats['users'] ?></h3>
            <p>Пользователей</p>
        </div>
        <div class="stat-card">
            <h3><?= $stats['teachers'] ?></h3>
            <p>Преподавателей</p>
        </div>
        <div class="stat-card">
            <h3><?= $stats['groups'] ?></h3>
            <p>Групп</p>
        </div>
        <div class="stat-card">
            <h3><?= $stats['lessons'] ?></h3>
            <p>Занятий в расписании</p>
        </div>
    </div>

    <!-- Блок 2: Детализация -->
    <div class="dashboard-info">
        <div class="stat-card">
            <h4>Топ-3 активных группы</h4>
            <ul>
                <?php foreach ($topGroups as $g): ?>
                    <li><?= htmlspecialchars($g['name']) ?> — <?= $g['count'] ?> занятий</li>
                <?php endforeach; ?>
            </ul>
        </div>

        <div class="stat-card">
            <h4>Последние регистрации</h4>
            <table style="width: 100%; font-size: 14px; margin-top: 10px;">
                <?php foreach ($recentUsers as $u): ?>
                    <tr>
                        <td style="padding: 5px 0;"><?= htmlspecialchars($u['name']) ?></td>
                        <td style="color: #64748b;"><?= date('d.m.y', strtotime($u['created_at'])) ?></td>
                    </tr>
                <?php endforeach; ?>
            </table>
        </div>
    </div>
</div>