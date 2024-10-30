
# Sintoniza

Este é um servidor de sincronização de podcast baseado no "protocolo" gPodder.
Esse projeto é um fork do [oPodSync](https://github.com/kd2org/opodsync)

### Aplicativos suportados

- [AntennaPod](https://github.com/AntennaPod/AntennaPod) - Android
- [gPodder](https://gpodder.github.io/) - Debian
- [Kasts](https://invent.kde.org/multimedia/kasts) - Linux/Android
- [PinePods](https://github.com/madeofpendletonwool/PinePods) - WebServer

## Docker

Apos instalar o docker, vamos criar um *compose*:

`curl -o ./docker-compose.yml https://raw.githubusercontent.com/manualdousuario/sintoniza/main/docker-compose.yml`

`nano docker-compose.yml`

```
services:
  lerama:
    container_name: sintoniza
    image: ghcr.io/manualdousuario/sintoniza/sintoniza:latest
    ports:
      - "80:80"
    environment:
      DB_HOST: mariadb
      DB_USERNAME: USUARIO
      DB_PASSWORD: SENHA
      DB_NAME: BANCO_DE_DADOS
      SITE_URL: https://sintoniza.xyz
      SITE_NAME: Sintoniza
      ADMIN_PASSWORD: p@ssw0rd
    depends_on:
      - db
services:
  db:
    image: mariadb:10.11
    container_name: db
    environment:
      MYSQL_ROOT_PASSWORD: SENHA_ROOT
      MYSQL_DATABASE: BANCO_DE_DADOS
      MYSQL_USER: USUARIO
      MYSQL_PASSWORD: SENHA
    ports:
      - 3306:3306
    volumes:
      - ./mariadb/data:/var/lib/mysql
```

Atualize as informações dos environments e em seguida pode rodar `docker compose up -d`
Todos as tags de environment são obrigatorias.

Antes de começar, precisamos criar as tabelas do banco de dados.

`docker exec -it db mysql -u USUARIO -pSENHA BANCO_DE_DADOS`

```
SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

DROP TABLE IF EXISTS `devices`;
CREATE TABLE `devices`  (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user` int(11) NOT NULL,
  `deviceid` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  `data` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE INDEX `deviceid`(`deviceid`, `user`) USING BTREE,
  INDEX `user`(`user`) USING BTREE,
  CONSTRAINT `devices_ibfk_1` FOREIGN KEY (`user`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE RESTRICT
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Dynamic;

DROP TABLE IF EXISTS `episodes`;
CREATE TABLE `episodes`  (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `feed` int(11) NOT NULL,
  `media_url` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `url` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  `image_url` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  `duration` int(11) NULL DEFAULT NULL,
  `title` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  `description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  `pubdate` datetime NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE INDEX `episodes_unique`(`feed`, `media_url`(255)) USING BTREE,
  CONSTRAINT `episodes_ibfk_1` FOREIGN KEY (`feed`) REFERENCES `feeds` (`id`) ON DELETE CASCADE ON UPDATE RESTRICT,
  CONSTRAINT `check_pubdate` CHECK (`pubdate` is null or `pubdate` = `pubdate`)
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Dynamic;

DROP TABLE IF EXISTS `episodes_actions`;
CREATE TABLE `episodes_actions`  (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user` int(11) NOT NULL,
  `subscription` int(11) NOT NULL,
  `episode` int(11) NULL DEFAULT NULL,
  `device` int(11) NULL DEFAULT NULL,
  `url` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `changed` int(11) NOT NULL,
  `action` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `data` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `subscription`(`subscription`) USING BTREE,
  INDEX `device`(`device`) USING BTREE,
  INDEX `episodes_idx`(`user`, `action`, `changed`) USING BTREE,
  INDEX `episodes_actions_link`(`episode`) USING BTREE,
  CONSTRAINT `episodes_actions_ibfk_1` FOREIGN KEY (`user`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE RESTRICT,
  CONSTRAINT `episodes_actions_ibfk_2` FOREIGN KEY (`subscription`) REFERENCES `subscriptions` (`id`) ON DELETE CASCADE ON UPDATE RESTRICT,
  CONSTRAINT `episodes_actions_ibfk_3` FOREIGN KEY (`episode`) REFERENCES `episodes` (`id`) ON DELETE SET NULL ON UPDATE RESTRICT,
  CONSTRAINT `episodes_actions_ibfk_4` FOREIGN KEY (`device`) REFERENCES `devices` (`id`) ON DELETE SET NULL ON UPDATE RESTRICT
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Dynamic;

DROP TABLE IF EXISTS `feeds`;
CREATE TABLE `feeds`  (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `feed_url` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `image_url` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  `url` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  `language` varchar(2) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  `title` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  `description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  `pubdate` datetime NULL DEFAULT current_timestamp(),
  `last_fetch` int(11) NOT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE INDEX `feed_url`(`feed_url`(255)) USING BTREE,
  CONSTRAINT `check_language` CHECK (`language` is null or octet_length(`language`) = 2),
  CONSTRAINT `check_pubdate` CHECK (`pubdate` is null or `pubdate` = `pubdate`)
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Dynamic;

DROP TABLE IF EXISTS `subscriptions`;
CREATE TABLE `subscriptions`  (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user` int(11) NOT NULL,
  `feed` int(11) NULL DEFAULT NULL,
  `url` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `deleted` tinyint(4) NOT NULL DEFAULT 0,
  `changed` int(11) NOT NULL,
  `data` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE INDEX `subscription_url`(`url`(255), `user`) USING BTREE,
  INDEX `user`(`user`) USING BTREE,
  INDEX `subscription_feed`(`feed`) USING BTREE,
  CONSTRAINT `subscriptions_ibfk_1` FOREIGN KEY (`user`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE RESTRICT,
  CONSTRAINT `subscriptions_ibfk_2` FOREIGN KEY (`feed`) REFERENCES `feeds` (`id`) ON DELETE SET NULL ON UPDATE RESTRICT
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Dynamic;

DROP TABLE IF EXISTS `users`;
CREATE TABLE `users`  (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `password` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `active` tinyint(1) NOT NULL DEFAULT 1,
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE INDEX `users_name`(`name`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 5 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Dynamic;

SET FOREIGN_KEY_CHECKS = 1;
```

Após executar o SQL, você pode verificar se as tabelas foram criadas com sucesso: `SHOW TABLES`;

## Informações adicionais

Recomendo que utilize o [NGINX Proxy Manager](https://nginxproxymanager.com/) como webservice a frente dessa imagem, isso dará mais proteção e camadas de cache.

As rotinas de coleta de dados irão rodar a cada hora e o log pode ser visto em `/var/log/lorema.log`

Uma instalação pública está disponivel em [PC do Manual](https://sintoniza.pcdomanual.com/) 