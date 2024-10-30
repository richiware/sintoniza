<div class="text-center mb-4">
    <h2 class="mb-3">Olá, <?= htmlspecialchars($user->name) ?>!</h2>
    <div class="alert alert-warning" role="alert">
        Usuário secreto do GPodder: <strong><?= $gpodder->getUserToken() ?></strong><br/>
        <small>(Use este nome de usuário no <i>GPodder Desktop</i>, pois ele não suporta senhas)</small>
    </div>
</div>

<div class="card shadow">
    <div class="card-body p-4">
        <h2 class="card-title fs-2 pb-3">Inscrições</h2>
        
        <?php if (empty($subscriptions)): ?>
            <div class="alert alert-info">
                Você ainda não tem nenhuma assinatura.
            </div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Podcast</th>
                            <th>Última atualização</th>
                            <th>Episodios</th>
                            <th></th>
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
                                        <i class="bi bi-eye"></i> Visualizar
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

<?php if (isset($_GET['oktoken'])): ?>
    <div class="alert alert-success text-center">
        Você está logado, pode fechar isso e voltar para o aplicativo.
    </div>
<?php endif ?> 