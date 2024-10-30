<div class="card shadow">
    <div class="card-body">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="card-title mb-0"><?= htmlspecialchars($feed->title) ?></h2>
            <a href="subscriptions" class="btn btn-outline-primary">
                <i class="bi bi-arrow-left"></i> Voltar
            </a>
        </div>

        <?php if ($feed->description): ?>
            <div class="alert alert-info">
                <?= nl2br(htmlspecialchars($feed->description)) ?>
            </div>
        <?php endif ?>

        <?php if (empty($episodes)): ?>
            <div class="alert alert-warning">Nenhum episodio encontrado.</div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Titulo</th>
                            <th>Publicado</th>
                            <th>Duração</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($episodes as $episode): ?>
                            <tr>
                                <td><?= htmlspecialchars($episode->title) ?></td>
                                <td>
                                    <?php if ($episode->pubdate): ?>
                                        <time datetime="<?= $episode->pubdate->format(DATE_ISO8601) ?>">
                                            <?= $episode->pubdate->format('d/m/Y H:i') ?>
                                        </time>
                                    <?php endif ?>
                                </td>
                                <td><?= $episode->duration ? gmdate('H:i:s', $episode->duration) : '-' ?></td>
                                <td>
                                    <?php if ($episode->played): ?>
                                        <span class="badge bg-success">Tocado</span>
                                    <?php else: ?>
                                        <span class="badge bg-secondary">Novo</span>
                                    <?php endif ?>
                                </td>
                            </tr>
                        <?php endforeach ?>
                    </tbody>
                </table>
            </div>
        <?php endif ?>
    </div>
</div> 