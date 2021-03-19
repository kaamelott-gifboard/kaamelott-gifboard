########################################
#           VENDORS
########################################
FROM composer:2.0.11 as vendors

COPY composer.json composer.lock /app/

RUN composer install \
    --no-ansi \
    --no-interaction \
    --no-progress \
    --prefer-dist \
    --no-dev \
    --no-suggest \
    --no-scripts \
    --no-autoloader

COPY src/ /app/src

RUN composer dump-autoload \
    --no-ansi \
    --no-interaction \
    --no-dev \
    --no-scripts \
    --classmap-authoritative

########################################
#           PRODUCTION
########################################
FROM php:8.0-fpm-alpine as prod

WORKDIR /var/www

RUN apk add --no-cache nginx supervisor \
    && docker-php-ext-install -j$(grep -c ^processor /proc/cpuinfo 2>/dev/null || 1) opcache \
    && mkdir var /run/supervisord \
    && chown -R www-data:www-data var \
    && cp /usr/local/etc/php/php.ini-production /usr/local/etc/php/php.ini

COPY docker/php/php-production.ini /usr/local/etc/php/php.ini
COPY docker/php/opcache.ini /usr/local/etc/php/conf.d/opcache.ini
COPY docker/supervisord/ /etc/supervisor/
COPY docker/nginx/nginx.conf /etc/nginx/nginx.conf
COPY --from=vendors /app/vendor /var/www/vendor
COPY . /var/www
RUN rm -rf \
    html \
    composer.* \
    docker \
    config/packages/dev \
    config/packages/test \
    config/services_test.yaml \
    config/routes/dev \
    && chmod a+x /etc/supervisor/kill-supervisord.sh

ENV PHP_USER=www-data PHP_GROUP=www-data

EXPOSE 8080

ENTRYPOINT ["/usr/bin/env"]
CMD ["/usr/bin/supervisord", "-c", "/etc/supervisor/supervisord.conf"]

########################################
#           DEV
########################################
FROM prod as dev

RUN apk add --no-cache ${PHPIZE_DEPS} \
    && pecl install xdebug-3.0.3 \
    && docker-php-ext-enable xdebug

COPY --from=vendors /usr/bin/composer /usr/bin/composer
