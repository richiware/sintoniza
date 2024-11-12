# Sintoniza

[![en](https://img.shields.io/badge/lang-en-red.svg)](https://github.com/manualdousuario/sintoniza/blob/master/README.md)
[![pt-br](https://img.shields.io/badge/lang-pt--br-green.svg)](https://github.com/manualdousuario/sintoniza/blob/master/README.pt-br.md)

This is a podcast synchronization server based on the gPodder protocol.
This project is a fork of [oPodSync](https://github.com/kd2org/opodsync)
Requires PHP 8.0+ and MySQL/MariaDB.

## Tested Applications

- [AntennaPod](https://github.com/AntennaPod/AntennaPod) 3.5.0 - Android

![AntennaPod 3.5.0](https://github.com/manualdousuario/sintoniza/blob/main/assets/antennapod_350.gif?raw=true)

- [Cardo](https://cardo-podcast.github.io) 1.90 - Windows/MacOS/Linux
- [Kasts](https://invent.kde.org/multimedia/kasts) 21.88 - [Windows](https://cdn.kde.org/ci-builds/multimedia/kasts/)/Android/Linux
- [gPodder](https://gpodder.github.io/) 3.11.4 - Windows/macOS/Linux/BSD

## Resources

- Compatible with GPodder and NextCloud gPodder
- Stores subscription and episode history
- Device-to-device synchronization
- Subscription and history management
- Global statistics
- Administrative area for user control
- Complete podcast and episode data

## Instalação via Docker

After installing Docker, create a *compose* file:

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
      DB_USERNAME: user
      DB_PASSWORD: password
      DB_NAME: database_name
      BASE_URL: https://sintoniza.xyz
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

Update the environment variables, then run `docker compose up -d`
All environment tags are mandatory.

## Informações adicionais

Use [NGINX Proxy Manager](https://nginxproxymanager.com/) as a frontend web service for this container to add security and caching layers.
Another web services like Caddy will also work correctly.

Logs and debug information can be found in `/app/logs`

A public installation is available at [PC do Manual](https://sintoniza.pcdomanual.com/)