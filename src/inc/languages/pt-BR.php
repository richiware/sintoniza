<?php
return [
    'general' => [
        'profile' => 'Perfil',
        'logout' => 'Sair',
        'login' => 'Entrar',
        'register' => 'Registrar',
        'welcome' => 'Bem-vindo',
        'language' => 'Idioma',
        'save' => 'Salvar',
        'home' => 'Início',
        'administration' => 'Administração',
        'subscriptions' => 'Inscrições',
        'podcast_sync' => 'Sincronização de Podcasts',
        'site_description' => 'Servidor de sincronização de podcast baseado no protocolo gPodder com suporte ao AntennaPod',
        'back' => 'Voltar',
        'add' => 'Adicionar',
        'delete' => 'Deletar',
        'download' => 'Download',
        'update' => 'Atualizar',
        'hello' => 'Olá',
        'duration' => 'Duração',
        'statistics' => 'Estatísticas',
        'username' => 'Usuário',
        'password' => 'Senha',
        'email' => 'Email',
        'min_password_length' => 'Senha (mínimo de 8 caracteres)',
        'latest_updates' => 'Últimas atualizações',
        'devices' => 'Dispositivos'
    ],
    'errors' => [
        'schema_file_not_found' => 'Arquivo de esquema mysql.sql não encontrado',
        'sql_error' => 'Erro ao executar o comando SQL: %s\nO comando foi: %s'
    ],
    'profile' => [
        'title' => 'Perfil do Usuário',
        'email' => 'Email',
        'change_password' => 'Alterar Senha',
        'current_password' => 'Senha Atual',
        'new_password' => 'Nova Senha',
        'confirm_password' => 'Confirmar Senha',
        'language_settings' => 'Configurações de Idioma',
        'select_language' => 'Selecionar Idioma',
        'settings_saved' => 'Configurações salvas com sucesso',
        'error_saving' => 'Erro ao salvar configurações',
        'language_updated' => 'Idioma atualizado com sucesso',
        'password_changed' => 'Senha alterada com sucesso',
        'passwords_dont_match' => 'As novas senhas não coincidem',
        'min_password_length' => 'Mínimo de 8 caracteres',
        'timezone_settings' => 'Configuração de fuso horário',
        'select_timezone' => 'Selecionar fuso horário',
        'timezone_updated' => 'Fuso horário atualizado com sucesso'
    ],
    'languages' => [
        'en' => 'Inglês',
        'pt-BR' => 'Português (Brasil)'
    ],
    'admin' => [
        'title' => 'Administração',
        'add_user' => 'Adicionar Novo Usuário',
        'user_list' => 'Lista de Usuários',
        'username' => 'Usuário',
        'password' => 'Senha',
        'confirm_delete' => 'Tem certeza que deseja deletar este usuário?',
        'user_deleted' => 'Usuário deletado com sucesso',
        'user_registered' => 'Usuário registrado com sucesso'
    ],
    'dashboard' => [
        'secret_user' => 'Usuário secreto do GPodder',
        'secret_user_note' => '(Use este nome de usuário no GPodder Desktop, pois ele não suporta senhas)',
        'latest_updates' => 'Últimas 10 atualizações',
        'registered_devices' => 'Dispositivos registrados',
        'no_info' => 'Nenhuma informação disponível neste feedNenhuma informação disponível neste feed',
        'last_update' => 'Última atualização',
        'update_all_metadata' => 'Atualizar todos os metadados dos feeds',
        'metadata_note' => 'A atualização de meta dados das inscrições está configurada para ser feita por rotinas diretamente no servidor, as atualização são feitas a cada uma hora.',
        'opml_feed' => 'Feed OPML'
    ],
    'devices' => [
        'mobile' => 'Mobile',
        'desktop' => 'Desktop',
        'unavailable' => 'Indisponível'
    ],
    'actions' => [
        'played' => 'Tocado',
        'downloaded' => 'Baixado',
        'deleted' => 'Deletado',
        'unavailable' => 'Indisponível',
        'on' => 'no',
        'at' => 'às'
    ],
    'messages' => [
        'subscriptions_disabled' => 'As assinaturas estão desabilitadas.',
        'invalid_captcha' => 'Captcha inválido.',
        'login_success' => 'Você está logado, pode fechar isso e voltar para o aplicativo.',
        'metadata_warning' => 'Os títulos e imagens dos episódios podem estar faltando devido a rastreadores/anúncios usados por alguns provedores de podcast.',
        'app_requesting_access' => 'Um aplicativo está solicitando acesso à sua conta.',
        'fill_captcha' => 'Preencha com seguinte número:',
        'auto_url_error' => 'Não é possível detectar automaticamente a URL do aplicativo. Defina a constante BASE_URL ou a variável de ambiente.',
        'invalid_url' => 'URL inválida:',
        'device_id_not_registered' => 'ID do dispositivo não registrado',
        'invalid_username' => 'Nome de usuário inválido',
        'invalid_username_password' => 'Nome de usuário/senha inválidos',
        'no_username_password' => 'Nenhum nome de usuário ou senha fornecidos',
        'session_cookie_required' => 'Cookie de sessão é necessário',
        'session_expired' => 'Cookie de ID de sessão expirado e nenhum cabeçalho de autorização foi fornecido',
        'user_not_exists' => 'O usuário não existe',
        'logged_out' => 'Desconectado',
        'unknown_login_action' => 'Ação de login desconhecida:',
        'invalid_gpodder_token' => 'Token gpodder inválido',
        'invalid_device_id' => 'ID do dispositivo inválido',
        'invalid_input_array' => 'Entrada inválida: requer uma matriz com uma linha por feed',
        'not_implemented' => 'Ainda não implementado',
        'invalid_array' => 'Nenhuma matriz válida encontrada',
        'missing_action_key' => 'Chave de ação ausente',
        'nextcloud_undefined_endpoint' => 'Ponto de extremidade da API Nextcloud indefinido',
        'output_format_not_implemented' => 'Formato de saída não implementado',
        'email_already_registered' => 'Endereço de e-mail já registrado',
    ],
    'statistics' => [
        'registered_users' => 'Usuários Registrados',
        'registered_devices' => 'Dispositivos Registrados',
        'top_10' => 'Top 10',
        'most_subscribed' => 'Mais Inscritos',
        'most_downloaded' => 'Mais Baixados',
        'most_played' => 'Mais Tocados'
    ],
    'footer' => [
        'managed_by' => 'Instância gerenciada e mantida por',
        'with_love_by' => 'Com ❤️ por',
        'version' => 'Versão'
    ],
    'home' => [
        'intro' => 'Este é um servidor de sincronização de podcast baseado no "protocolo" gPodder.',
        'fork_note' => 'Esse projeto é um fork do',
        'github_project' => 'Projeto publicado no Github',
        'tested_apps' => 'Aplicativos testados'
    ]
];
