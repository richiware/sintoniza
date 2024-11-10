<?php

echo '<h2>Administrativo</h2>';

// Add new user form
echo '<div class="card mb-4">
    <div class="card-body">
        <h3 class="card-title">Adicionar Novo Usuário</h3>
        <form method="post" action="" class="row g-3">
            <div class="col-md-3">
                <label for="new_username" class="form-label">Usuário</label>
                <input type="text" class="form-control" name="new_username" id="new_username" required>
            </div>
            <div class="col-md-3">
                <label for="new_password" class="form-label">Senha</label>
                <input type="password" class="form-control" name="new_password" id="new_password" required minlength="8">
            </div>
            <div class="col-md-4">
                <label for="new_email" class="form-label">Email</label>
                <input type="email" class="form-control" name="new_email" id="new_email" required minlength="8">
            </div>
            <div class="col-md-2 d-flex align-items-end">
                <button type="submit" class="btn btn-primary w-100">
                    <i class="bi bi-person-plus"></i> Adicionar
                </button>
            </div>
        </form>
    </div>
</div>';

// Users list
echo '<div class="card">
    <div class="card-body">
        <h3 class="card-title">Lista de Usuários</h3>
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Usuário</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody>';

    $users = $db->all('SELECT id, name FROM users ORDER BY id DESC');
    foreach ($users as $user) {
        printf('<tr>
            <td>%d</td>
            <td>%s</td>
            <td>
                <form method="post" action="" class="d-inline" onsubmit="return confirm(\'Tem certeza que deseja deletar este usuário?\');">
                    <input type="hidden" name="delete_user" value="%d">
                    <button type="submit" class="btn btn-danger btn-sm">
                        <i class="bi bi-trash"></i> Deletar
                    </button>
                </form>
            </td>
        </tr>',
            $user->id,
            htmlspecialchars($user->name),
            $user->id
        );
    }

    echo '</tbody></table>
        </div>
    </div>';