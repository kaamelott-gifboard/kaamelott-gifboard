FROM php:7.4-fpm-alpine
WORKDIR /var/www
RUN apk add --update \
	curl \
	git \
	unzip \
	libzip-dev \
	zlib-dev; \
	docker-php-ext-install zip
COPY --from=composer /usr/bin/composer /usr/bin/composer
