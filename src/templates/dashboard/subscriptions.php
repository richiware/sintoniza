<h2 class="fs-3 mb-3"><?php echo __('general.subscriptions');?></h2>

<?php
if (isset($_POST['update']) && !DISABLE_USER_METADATA_UPDATE) { ?>
    <p><a href="/dashboard/subscriptions" class="btn btn-danger" aria-label="<?php echo __('general.back');?>"><?php echo __('general.back');?></a></p>
    <?php
    $gpodder->updateAllFeeds();
    exit;
}
elseif (isset($_GET['id'])) {
    ?>
        <p>
            <a href="/dashboard/subscriptions"class="btn btn-danger" aria-label="<?php echo __('general.back');?>"><?php echo __('general.back');?></a>
        </p>
    <?php
    $feed = $gpodder->getFeedForSubscription((int)$_GET['id']);
    if (isset($feed->url, $feed->title, $feed->image_url, $feed->description)) {
        ?>
            <div class="row">
                <div class="col-12 col-md-2">
                    <img class="rounded w-100 h-auto border" width="150" height="150" src="<?php echo $feed->image_url; ?>">
                </div>
                    <div class="col-12 col-md-10">
                        <h2 class="fs-3">
                            <a href="<?php echo htmlspecialchars($feed->url); ?>" class="link-dark" target="_blank"><?php echo htmlspecialchars($feed->title); ?></a>
                        </span>
                    </h2>
                    <p><?php echo format_description($feed->description); ?></p>
                </div>
            </div>
            <div class="alert alert-warning mt-3" role="alert">
                Os títulos e imagens dos episódios podem estar faltando devido a rastreadores/anúncios usados ​​por alguns provedores de podcast.<br/>
            </div>
        <?php
    }
    else { ?>
        <div class="alert alert-warning mt-3" role="alert"><?php echo __('dashboard.no_info'); ?></div>
    <?php }
    ?>
    <ul class="list-group">
        <?php
            foreach ($gpodder->listActions((int)$_GET['id']) as $row) {
                $url = strtok(basename($row->url), '?');
                strtok('');
                $title = $row->title ?? $url;
                $image_url = !empty($row->image_url) ? '<div class="thumbnail"><img class="rounded border" src="'.$row->image_url.'" width="80" height="80" /></div>' : '' ;
            
                if($row->action == 'play') {
                    $action = '<div class="badge text-bg-success rounded-pill"><i class="bi bi-play"></i> '.__('actions.played').'</div>';
                } else if($row->action == 'download') {
                    $action = '<div class="badge text-bg-primary rounded-pill"><i class="bi bi-download"></i> '.__('actions.downloaded').'</div>';
                } else if($row->action == 'delete') {
                    $action = '<div class="badge text-bg-danger rounded-pill"><i class="bi bi-trash-fill"></i> '.__('actions.deleted').'</div>';
                } else {
                    $action = '<div class="badge text-bg-secondary rounded-pill"><i class="bi bi-motherboard"></i> '.__('actions.unavailable').'</div>';
                }

                $device_name = $row->device_name ? '<div class="badge text-bg-primary rounded-pill">'.$row->device_name.'</div>' : '<div class="badge text-bg-secondary rounded-pill"><i class="bi bi-motherboard"></i> Indisponivel</div>';
                $duration = gmdate("H:i:s", $row->duration);
                ?>

                    <li class="list-group-item p-3">
                        <div class="meta pb-2">
                            <?php echo $action; ?> no <?php echo $device_name; ?> em <small><time datetime="<?php echo date(DATE_ISO8601, $row->changed); ?>"><?php echo date('d/m/Y \à\s H:i', $row->changed); ?></time></small>
                        </div>
                        <div class="episode_info d-flex flex-wrap gap-3">
                            <?php echo $image_url; ?>
                            <div class="data">
                                <a class="link-dark" target="_blank" href="<?php echo $row->episode_url; ?>"><?php echo htmlspecialchars($title); ?></a><br/>
                                <?php echo __('general.duration'); ?>: <?php echo $duration; ?><br/>
                                <a href="<?php echo htmlspecialchars($row->url); ?>" target="_blank" class="btn btn-sm btn-secondary"><i class="bi bi-cloud-arrow-down-fill"></i> Download</a>
                            </div>
                        </div>
                    </li>
                <?php
            }
        ?>
    </ul>
<?php } else { 
    ?>
    <form method="post" action="">
        <div class="flex-wrap d-flex gap-2 pb-4">
            <a href="/dashboard" class="btn btn-danger" aria-label="Voltar">Voltar</a>
            <a href="/subscriptions/<?php echo htmlspecialchars($gpodder->user->name); ?>.opml" target="_blank" class="btn btn-secondary">Feed OPML</a>
            <?php if(DISABLE_USER_METADATA_UPDATE == false) { ?>
                <button type="submit" class="btn btn-info" name="update" value=1>Atualizar todos os metadados dos feeds</button>
            <?php } ?>
        </div>
    </form>
    <?php if(DISABLE_USER_METADATA_UPDATE) { ?>
        <div class="alert alert-warning">A atualização de meta dados das inscrições está configurada para ser feita por rotinas diretamente no servidor, as atualização são feitas a cada uma hora.</div>
    <?php } ?>
    <?php

    echo '<ul class="list-group">';

    foreach ($gpodder->listActiveSubscriptions() as $row) {
        $image_url = !empty($row->image_url) ? '<div class="thumbnail"><img class="rounded border h-auto" src="'.$row->image_url.'" width="80" /></div>' : '' ;
        $title = $row->title ?? str_replace(['http://', 'https://'], '', $row->url);
            printf('
            <li class="list-group-item p-3">
                <div class="episode_info d-flex gap-3">
                    %s
                    <div class="data">
                        <h2 class="fs-5"><a class="link-dark" href="/dashboard/subscriptions?id=%d">%s</a></h2>
                        <small class="d-block">%s</small>
                        <small><strong>%s</strong>: <time datetime="%s" class="text-nowrap">%s</time></small>
                    </div>
                </div>
            </li>',
            $image_url,
            $row->id,
            htmlspecialchars($title),
            format_description($row->description),
            __('dashboard.last_update'),
            date(DATE_ISO8601, $row->last_change),
            date('d/m/Y \à\s H:i', $row->last_change)
        );
    }

    echo '</ul>';
}