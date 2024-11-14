# Sintoniza

[![en](https://img.shields.io/badge/lang-en-red.svg)](https://github.com/manualdousuario/sintoniza/blob/master/README.md)
[![pt-br](https://img.shields.io/badge/lang-pt--br-green.svg)](https://github.com/manualdousuario/sintoniza/blob/master/README.pt-br.md)

Este é um servidor de sincronização de podcast baseado no protocolo gPodder.
Esse projeto é um fork do [oPodSync](https://github.com/kd2org/opodsync)
Requer PHP 8.0+ e MySQL/MariaDB

## Aplicativos testados

- [AntennaPod](https://github.com/AntennaPod/AntennaPod) 3.5.0 - Android

![AntennaPod 3.5.0](https://github.com/manualdousuario/sintoniza/blob/main/assets/antennapod_350.gif?raw=true)

- [Cardo](https://cardo-podcast.github.io) 1.90 - Windows/MacOS/Linux
- [Kasts](https://invent.kde.org/multimedia/kasts) 21.88 - [Windows](https://cdn.kde.org/ci-builds/multimedia/kasts/)/Android/Linux
- [gPodder](https://gpodder.github.io/) 3.11.4 - Windows/macOS/Linux/BSD

## Recursos

- Compatível com GPodder e NextCloud gPodder
- Armazena histórico de assinaturas e episódios
- Sincronização entre dispositivos
- Assinaturas e histórico
- Estatisticas globais
- Area administrativa para controle de usuarios
- Dados completos dos podcasts e episodios

## Instalação via Docker

Após instalar o Docker, vamos criar um *compose*:

`curl -o ./docker-compose.yml https://raw.githubusercontent.com/manualdousuario/sintoniza/main/docker-compose.yml`

`nano docker-compose.yml`

```
services:
  sintoniza:
    container_name: sintoniza
    image: ghcr.io/manualdousuario/sintoniza/sintoniza:latest
    ports:
      - "80:80"
    environment:
      DB_HOST: mariadb
      DB_USER: user
      DB_PASS: password
      DB_NAME: database_name
      BASE_URL: https://sintoniza.xyz/
      TITLE: Sintoniza
      ADMIN_PASSWORD: p@ssw0rd
      DEBUG: true
      ENABLE_SUBSCRIPTIONS: true
      DISABLE_USER_METADATA_UPDATE: false
      SMTP_USER: email@email.com
      SMTP_PASS: password
      SMTP_HOST: smtp.email.com
      SMTP_FROM: email@email.com
      SMTP_NAME: "Sintoniza"
      SMTP_PORT: 587
      SMTP_SECURE: tls
      SMTP_AUTH: true
    depends_on:
      - db
services:
  db:
    image: mariadb:10.11
    container_name: db
    environment:
      MYSQL_ROOT_PASSWORD: root_password
      MYSQL_DATABASE: database_name
      MYSQL_USER: database_user
      MYSQL_PASSWORD: database_password
    ports:
      - 3306:3306
    volumes:
      - ./mariadb/data:/var/lib/mysql
```

Atualize as informações dos environments e em seguida pode rodar `docker compose up -d`
Todos as tags de environment são obrigatorias.

## Informações adicionais

Utilize o [NGINX Proxy Manager](https://nginxproxymanager.com/) como webservice a frente desse container, isso dará mais proteção e camadas de cache.
Outros webservices como Caddy tambem funcionarão corretamente.

Logs e debugs podem encontrados em `/app/logs`

Uma instalação pública está disponivel em [PC do Manual](https://sintoniza.pcdomanual.com/)