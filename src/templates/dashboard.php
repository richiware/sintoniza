<?php

echo '<div class="text-center mb-4">';
printf('<h2 class="mb-3">Olá, <strong>%s</strong>!</h2>', $gpodder->user->name);
echo '<div class="alert alert-warning" role="alert">';
printf('Usuário secreto do GPodder: <strong>%s</strong>', $gpodder->getUserToken());
echo '<small class="d-block">(Use este nome de usuário no <i>GPodder Desktop</i>, pois ele não suporta senhas)</small>';
echo '</div>';
echo '</div>';

?>
    <ul class="nav nav-tabs" id="myTab" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active" id="latest-tab" data-bs-toggle="tab" data-bs-target="#latest" type="button" role="tab" aria-controls="latest" aria-selected="true">
                Últimas atualizações
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="devices-tab" data-bs-toggle="tab" data-bs-target="#devices" type="button" role="tab" aria-controls="devices" aria-selected="false">
                Dispositivos
            </button>
        </li>
    </ul>
    <div class="tab-content" id="dashboard">
        <div class="tab-pane fade show active border border-top-0 bg-white rounded-bottom" id="latest" role="tabpanel" aria-labelledby="latest-tab">
            <?php
                // Latest subscriptions
                $subscriptions = $gpodder->listActiveSubscriptions();
                $actions = [];
                
                foreach ($subscriptions as $sub) {
                    $feed_actions = $gpodder->listActions($sub->id);
                    $actions = array_merge($actions, $feed_actions);
                }

                usort($actions, function($a, $b) {
                    return $b->changed - $a->changed;
                });

                $actions = array_slice($actions, 0, 10);
                
                if (!empty($actions)) {
                    echo '<ul class="list-group p-3">';
                    
                    foreach ($actions as $row) {
                        $url = strtok(basename($row->url), '?');
                        strtok('');
                        $title = $row->title ?? $url;
                        $image_url = !empty($row->image_url) ? '<div class="thumbnail"><img class="rounded border" src="'.$row->image_url.'" width="80" height="80" /></div>' : '' ;

                        if($row->action == 'play') {
                            $action = '<div class="badge text-bg-success rounded-pill"><i class="bi bi-play"></i> Tocado</div>';
                        } else if($row->action == 'download') {
                            $action = '<div class="badge text-bg-primary rounded-pill"><i class="bi bi-download"></i> Baixado</div>';
                        } else if($row->action == 'delete') {
                            $action = '<div class="badge text-bg-danger rounded-pill"><i class="bi bi-trash-fill"></i> Deletado</div>';
                        } else {
                            $action = '<div class="badge text-bg-secondary rounded-pill"><i class="bi bi-motherboard"></i> Indisponivel</div>';
                        }

                        $device_name = $row->device_name ? '<div class="badge text-bg-primary rounded-pill">'.$row->device_name.'</div>' : '<div class="badge text-bg-secondary rounded-pill"><i class="bi bi-motherboard"></i> Indisponivel</div>';
                        $duration = gmdate("H:i:s", $row->duration);

                        printf('<li class="list-group-item p-3">
                                <div class="meta pb-2">
                                    %s no %s em <small><time datetime="%s">%s</time></small>
                                </div>
                                <div class="episode_info d-flex gap-3">
                                    %s
                                    <div class="data">
                                        <a class="link-dark" target="_blank" href="%s">%s</a><br/>
                                        Duração: %s<br/>
                                        <a href="%s" target="_blank" class="btn btn-sm btn-secondary"><i class="bi bi-cloud-arrow-down-fill"></i> Download</a>
                                    </div>
                                </div>
                            </li>',
                            $action,
                            $device_name,
                            date(DATE_ISO8601, $row->changed),
                            date('d/m/Y \à\s H:i', $row->changed),
                            $image_url,
                            $row->episode_url,
                            htmlspecialchars($title),
                            $duration,
                            htmlspecialchars($row->url),
                        );
                    }
                    
                    echo '</ul>';
                }
            ?>
        </div>
        <div class="tab-pane fade border border-top-0 bg-white rounded-bottom" id="devices" role="tabpanel" aria-labelledby="devices-tab">
            <?php
                // Get user's registered devices
                $devices = $db->all('SELECT * FROM devices WHERE user = ? ORDER BY name', $gpodder->user->id);
                
                if (!empty($devices)) {
                    echo '<div class="list-group p-3">';
                    foreach ($devices as $device) {
                        $data = json_decode($device->data, true);
                        if($data['type'] == 'mobile') {
                            $device_type = 'bi-phone';
                        } else {
                            $device_type = 'bi-window';
                        }
                        printf('<div class="list-group-item py-2 px-3">
                            <div class="d-flex align-items-center">
                                <i class="bi %s fs-4 me-2"></i>
                                <div>
                                    <strong>%s</strong>
                                </div>
                            </div>
                        </div>',
                            $device_type,
                            htmlspecialchars($device->name)
                        );
                    }
                    echo '</div>';
                }
            ?>
        </div>
    </div>