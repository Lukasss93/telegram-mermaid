FROM php:8.6-apache

COPY --from=composer:2 /usr/bin/composer /usr/local/bin/composer

ARG XDEBUG_VERSION=3.6.0alpha1
RUN apt-get update \
    && apt-get install -y --no-install-recommends git libonig-dev libpq-dev libzip-dev unzip \
    && rm -rf /var/lib/apt/lists/* \
    && curl -fsSL "https://xdebug.org/files/xdebug-${XDEBUG_VERSION}.tgz" -o /tmp/xdebug.tgz \
    && tar -xzf /tmp/xdebug.tgz -C /tmp "xdebug-${XDEBUG_VERSION}" \
    && docker-php-ext-install -j"$(nproc)" mbstring pdo_mysql pdo_pgsql zip "/tmp/xdebug-${XDEBUG_VERSION}" \
    && rm -rf /tmp/xdebug.tgz "/tmp/xdebug-${XDEBUG_VERSION}" \
    && a2enmod rewrite
