# 🎧 Sintoniza

[![en](https://img.shields.io/badge/lang-en-red.svg)](https://github.com/manualdousuario/sintoniza/blob/master/README.md)
[![pt-br](https://img.shields.io/badge/lang-pt--br-green.svg)](https://github.com/manualdousuario/sintoniza/blob/master/README.pt-br.md)

Sintoniza is a powerful podcast synchronization server based on the gPodder protocol. It helps you keep your podcast subscriptions, episodes, and listening history in sync across all your devices.

This project is a fork of [oPodSync](https://github.com/kd2org/opodsync).

## ✨ Features

- Full compatibility with GPodder and NextCloud gPodder
- Smart subscription and episode history tracking
- Seamless device-to-device synchronization
- Complete podcast and episode metadata
- Global statistics dashboard
- Administrative interface for user management
- Built with PHP 8.0+ and MySQL/MariaDB

## 📱 Tested Applications

- [AntennaPod](https://github.com/AntennaPod/AntennaPod) 3.5.0 - Android

![AntennaPod 3.5.0](https://github.com/manualdousuario/sintoniza/blob/main/assets/antennapod_350.gif?raw=true)

- [Cardo](https://cardo-podcast.github.io) 1.90 - Windows/MacOS/Linux
- [Kasts](https://invent.kde.org/multimedia/kasts) 21.88 - [Windows](https://cdn.kde.org/ci-builds/multimedia/kasts/)/Android/Linux
- [gPodder](https://gpodder.github.io/) 3.11.4 - Windows/macOS/Linux/BSD

## 🐳 Docker Installation

### Prerequisites

You only need:
- Docker and docker compose

### Setup

1. First, get the compose file:
```bash
curl -o ./docker-compose.yml https://raw.githubusercontent.com/manualdousuario/sintoniza/main/docker-compose.yml
```

2. Configure the settings:
```bash
nano docker-compose.yml
```

3. Update the following configuration:
```yaml
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

Note: All environment variables are required.

4. Start the services:
```bash
docker compose up -d
```

## 🛠️ Maintenance

### Logs

View application logs:
```bash
docker-compose logs sintoniza
```

Debug information can be found in `/app/logs`

### Security

It's recommended to use [NGINX Proxy Manager](https://nginxproxymanager.com/) as a frontend web service for this container to add security and caching layers. Other web services like Caddy will also work correctly.

---

Made with ❤️! If you have questions or suggestions, open an issue and we'll help! 😉

A public instance is available at [PC do Manual](https://sintoniza.pcdomanual.com/)
