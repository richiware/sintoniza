<?php
    require_once __DIR__ . '/../config.php';
    $db = new DB(DB_HOST, DB_NAME, DB_USER, DB_PASS);
    function formatNumber($num) {
        return number_format($num, 0, ',', '.');
    }
    
    // Buscar contagens básicas
    $totalUsers = $db->firstColumn("SELECT COUNT(*) FROM users");
    $totalDevices = $db->firstColumn("SELECT COUNT(*) FROM devices");
    
    // Top 10 feeds mais inscritos
    $topFeeds = $db->all("
        SELECT 
            f.title,
            f.feed_url,
            COUNT(s.id) as subscription_count
        FROM feeds f
        JOIN subscriptions s ON s.feed = f.id
        WHERE s.deleted = 0
        GROUP BY f.id
        ORDER BY subscription_count DESC
        LIMIT 10
    ");
    
    // Top 10 episódios mais baixados
    $topDownloaded = $db->all("
        SELECT 
            e.title,
            f.title as feed_title,
            COUNT(ea.id) as download_count
        FROM episodes e
        JOIN feeds f ON e.feed = f.id
        JOIN episodes_actions ea ON ea.episode = e.id
        WHERE ea.action = 'download'
        GROUP BY e.id
        ORDER BY download_count DESC
        LIMIT 10
    ");
    
    // Top 10 episódios mais tocados
    $topPlayed = $db->all("
        SELECT 
            e.title,
            f.title as feed_title,
            COUNT(ea.id) as play_count
        FROM episodes e
        JOIN feeds f ON e.feed = f.id
        JOIN episodes_actions ea ON ea.episode = e.id
        WHERE ea.action = 'play'
        GROUP BY e.id
        ORDER BY play_count DESC
        LIMIT 10
    ");
?>
<h2 class="fs-3 mb-3"><?php echo __('general.statistics');?></h2>

<div class="row g-4 mb-4">
    <div class="col-md-6">
        <div class="card h-100">
            <div class="card-body">
                <h5 class="card-title">Usuários Registrados</h5>
                <p class="display-5 text-primary mb-0"><?= formatNumber($totalUsers) ?></p>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card h-100">
            <div class="card-body">
                <h5 class="card-title">Dispositivos Registrados</h5>
                <p class="display-5 text-success mb-0"><?= formatNumber($totalDevices) ?></p>
            </div>
        </div>
    </div>
</div>

<!-- Top Feeds -->
<div class="card mb-4">
    <div class="card-header bg-white">
        <h2 class="h4 mb-0">Top 10 Feeds Mais Inscritos</h2>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover">
                <thead class="table-light">
                    <tr>
                        <th scope="col">#</th>
                        <th scope="col">Feed</th>
                        <th scope="col" class="text-end">Inscrições</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($topFeeds as $i => $feed): ?>
                    <tr>
                        <th scope="row"><?= $i + 1 ?>º</th>
                        <td><?= htmlspecialchars($feed->title) ?></td>
                        <td class="text-end"><?= formatNumber($feed->subscription_count) ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Top Downloads -->
<div class="card mb-4">
    <div class="card-header bg-white">
        <h2 class="h4 mb-0">Top 10 Episódios Mais Baixados</h2>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover">
                <thead class="table-light">
                    <tr>
                        <th scope="col">#</th>
                        <th scope="col">Episódio</th>
                        <th scope="col">Feed</th>
                        <th scope="col" class="text-end">Downloads</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($topDownloaded as $i => $episode): ?>
                    <tr>
                        <th scope="row"><?= $i + 1 ?>º</th>
                        <td><?= htmlspecialchars($episode->title) ?></td>
                        <td><?= htmlspecialchars($episode->feed_title) ?></td>
                        <td class="text-end"><?= formatNumber($episode->download_count) ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Top Played -->
<div class="card">
    <div class="card-header bg-white">
        <h2 class="h4 mb-0">Top 10 Episódios Mais Tocados</h2>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover">
                <thead class="table-light">
                    <tr>
                        <th scope="col">#</th>
                        <th scope="col">Episódio</th>
                        <th scope="col">Feed</th>
                        <th scope="col" class="text-end">Reproduções</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($topPlayed as $i => $episode): ?>
                    <tr>
                        <th scope="row"><?= $i + 1 ?>º</th>
                        <td><?= htmlspecialchars($episode->title) ?></td>
                        <td><?= htmlspecialchars($episode->feed_title) ?></td>
                        <td class="text-end"><?= formatNumber($episode->play_count) ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>