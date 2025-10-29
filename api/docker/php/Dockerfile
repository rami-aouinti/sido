FROM php:8.2-fpm

RUN set -eux; \
    apt-get update; \
    apt-get install -y --no-install-recommends \
        git \
        unzip \
        libpq-dev \
        libicu-dev \
        libzip-dev \
        libonig-dev \
        libxml2-dev; \
    docker-php-ext-configure intl; \
    docker-php-ext-install -j"$(nproc)" intl mbstring opcache pdo pdo_pgsql zip; \
    pecl install apcu; \
    docker-php-ext-enable apcu; \
    rm -rf /var/lib/apt/lists/*

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /srv/api

COPY composer.json composer.lock symfony.lock* ./
RUN set -eux; \
    composer install \
        --no-interaction \
        --prefer-dist \
        --optimize-autoloader

COPY . .

RUN set -eux; \
    composer install \
        --no-interaction \
        --prefer-dist \
        --optimize-autoloader; \
    mkdir -p var; \
    chown -R www-data:www-data var

USER www-data

CMD ["php-fpm"]
