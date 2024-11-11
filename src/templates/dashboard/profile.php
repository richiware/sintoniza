
<h2 class="fs-3 mb-3"><?php echo __('general.profile');?></h2>

<ul class="nav nav-tabs" id="myTab" role="tablist">
    <li class="nav-item" role="presentation">
        <button class="nav-link active" id="language_settings-tab" data-bs-toggle="tab" data-bs-target="#language_settings" type="button" role="tab" aria-controls="language_settings" aria-selected="true">
            <?php echo __('profile.language_settings'); ?>
        </button>
    </li>
    <li class="nav-item" role="presentation">
        <button class="nav-link" id="change_password-tab" data-bs-toggle="tab" data-bs-target="#change_password" type="button" role="tab" aria-controls="change_password" aria-selected="false">
            <?php echo __('profile.change_password'); ?>
        </button>
    </li>
</ul>
<div class="tab-content" id="dashboard">
    <div class="tab-pane fade show active border border-top-0 bg-white rounded-bottom" id="language_settings" role="tabpanel" aria-labelledby="language_settings-tab">
        <form method="post" action="/dashboard/profile" class="p-3">
            <div class="form-group mb-3">
                <label for="language"><?php echo __('profile.select_language'); ?></label>
                <?php
                    if (isset($_POST['language'])) {
                        $result = $gpodder->updateLanguage($_POST['language']);
                        if ($result === null) {
                            echo '<div class="alert alert-success" role="alert">' . __('profile.language_updated') . '</div>';
                        } else {
                            printf('<div class="alert alert-danger" role="alert">%s</div>', htmlspecialchars($result));
                        }
                    }
                ?>
                <select name="language" id="language" class="form-control">
                    <?php
                    $currentLang = Language::getInstance()->getCurrentLanguage();
                    
                    foreach (Language::getInstance()->getAvailableLanguages() as $code => $name) {
                        $selected = $code === $currentLang ? 'selected' : '';
                        echo "<option value=\"{$code}\" {$selected}>{$name}</option>";
                    }
                    ?>
                </select>
            </div>
            <button type="submit" class="btn btn-primary">
                <?php echo __('general.save'); ?>
            </button>
        </form>
    </div>
    <div class="tab-pane fade border border-top-0 bg-white rounded-bottom" id="change_password" role="tabpanel" aria-labelledby="change_password-tab">
        <form method="post" action="/dashboard/profile" class="p-3">
            <?php
                if (isset($_POST['change_password'])) {
                    if ($_POST['new_password'] !== $_POST['confirm_password']) {
                        echo '<div class="alert alert-danger" role="alert">As novas senhas não coincidem.</div>';
                    }
                    else {
                        $result = $gpodder->changePassword($_POST['current_password'], $_POST['new_password']);
                        if ($result === null) {
                            echo '<div class="alert alert-success" role="alert">Senha alterada com sucesso!</div>';
                        }
                        else {
                            printf('<div class="alert alert-danger" role="alert">%s</div>', htmlspecialchars($result));
                        }
                    }
                }
            ?>
            <div class="mb-3">
                <label for="current_password" class="form-label">Senha atual:</label>
                <input type="password" class="form-control" required name="current_password" id="current_password" />
            </div>
            <div class="mb-3">
                <label for="new_password" class="form-label">Nova Senha (mínimo 8 caracteres):</label>
                <input type="password" class="form-control" required name="new_password" id="new_password" minlength="8" />
            </div>
            <div class="mb-3">
                <label for="confirm_password" class="form-label">Confirmar nova Senha:</label>
                <input type="password" class="form-control" required name="confirm_password" id="confirm_password" minlength="8" />
            </div>
            <button type="submit" name="change_password" class="btn btn-primary">
                Alterar
            </button>
        </form>
    </div>
</div>