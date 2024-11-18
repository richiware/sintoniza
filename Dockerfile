FROM php:8.0-fpm

RUN apt-get update && apt-get install -y nginx cron nano procps unzip git \
    && docker-php-ext-install pdo_mysql
	
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

COPY default.conf /etc/nginx/sites-available/default

RUN mkdir -p /app
COPY src/ /app/

WORKDIR /app
RUN composer install --no-interaction --optimize-autoloader

COPY env.sh /usr/local/bin/env.sh
RUN chmod +x /usr/local/bin/env.sh

COPY start.sh /usr/local/bin/start.sh
RUN chmod +x /usr/local/bin/start.sh

RUN touch /app/logs/cron.log
RUN echo '* */12 * * * root php "/app/cli/update_feeds_metadata.php" >> /app/logs/cron.log 2>&1' >> /etc/crontab
RUN echo '0 4 * * * root php "/app/cli/update_statistics_cache.php" >> /app/logs/cron.log 2>&1' >> /etc/crontab

RUN chown -R www-data:www-data /app && chmod -R 755 /app

EXPOSE 80

CMD ["/bin/bash", "-c", "/usr/local/bin/env.sh && /usr/local/bin/start.sh"]
