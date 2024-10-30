<div class="card shadow">
    <div class="card-body p-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="card-title mb-0">Usuários</h2>
            <a href="register" class="btn btn-primary">
                <i class="bi bi-person-plus"></i> Registrar usuário
            </a>
        </div>

        <?php if (empty($users)): ?>
            <div class="alert alert-info">Nenhum usuario registrado.</div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Usuário</th>
                            <th>Status</th>
                            <th>Incrições</th>
                            <th>Ultima atualização</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($users as $user): ?>
                            <tr>
                                <td><?= htmlspecialchars($user->name) ?></td>
                                <td>
                                    <?php if ($user->active): ?>
                                        <span class="badge bg-success">Active</span>
                                    <?php else: ?>
                                        <span class="badge bg-danger">Inactive</span>
                                    <?php endif ?>
                                </td>
                                <td><?= $user->subscription_count ?></td>
                                <td>
                                    <?= $user->last_activity ? date('Y-m-d H:i', $user->last_activity) : 'Never' ?>
                                </td>
                                <td>
                                    <div class="btn-group">
                                        <form method="post" action="" class="d-inline">
                                            <input type="hidden" name="action" value="toggle_user_status">
                                            <input type="hidden" name="user_id" value="<?= $user->id ?>">
                                            <button type="submit" class="btn btn-warning btn-sm">
                                                <i class="bi bi-<?= $user->active ? 'pause' : 'play' ?>-fill"></i> 
                                                <?= $user->active ? 'Desabilitar' : 'Habilitar' ?>
                                            </button>
                                        </form>
                                        <form method="post" action="" class="d-inline ms-2" 
                                            onsubmit="return confirm('Tem certeza de que deseja excluir este usuário?')">
                                            <input type="hidden" name="action" value="delete_user">
                                            <input type="hidden" name="user_id" value="<?= $user->id ?>">
                                            <button type="submit" class="btn btn-danger btn-sm">
                                                <i class="bi bi-trash"></i> Deletar
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach ?>
                    </tbody>
                </table>
            </div>
        <?php endif ?>
    </div>
</div> 