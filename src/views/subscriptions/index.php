<div class="card shadow">
    <div class="card-body p-4">
        <h2 class="card-title">My Subscriptions</h2>
        
        <?php if (empty($subscriptions)): ?>
            <div class="alert alert-info">
                You don't have any subscriptions yet.
            </div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Podcast</th>
                            <th>Last Update</th>
                            <th>Episodes</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($subscriptions as $sub): ?>
                            <tr>
                                <td>
                                    <a href="?id=<?= $sub->id ?>" class="text-decoration-none">
                                        <?= htmlspecialchars($sub->title ?? str_replace(['http://', 'https://'], '', $sub->url)) ?>
                                    </a>
                                </td>
                                <td>
                                    <time datetime="<?= date(DATE_ISO8601, $sub->last_change) ?>">
                                        <?= date('d/m/Y H:i', $sub->last_change) ?>
                                    </time>
                                </td>
                                <td><?= $sub->count ?></td>
                                <td>
                                    <a href="?id=<?= $sub->id ?>" class="btn btn-sm btn-primary">
                                        <i class="bi bi-eye"></i> View
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach ?>
                    </tbody>
                </table>
            </div>
        <?php endif ?>
    </div>
</div> 