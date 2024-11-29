ARG PHP_VERSION=8.2.7
ARG DEBIAN_RELEASE=bullseye

FROM php:${PHP_VERSION}-cli-${DEBIAN_RELEASE}

ARG COMPOSER_ALLOW_SUPERUSER=1
ARG COMPOSER_NO_INTERACTION=1
ARG COMPOSER_VERSION=2.7.6
ARG DEBIAN_FRONTEND=noninteractive
ARG DOCKER_PHP_DEPS='libxml2-dev libzip-dev unzip'
ARG XDEBUG_VERSION=3.2.1

RUN set -xe; \
    apt-get --allow-releaseinfo-change update && \
    apt-get install -y -qq ${DOCKER_PHP_DEPS} --no-install-suggests --no-install-recommends && \
    docker-php-ext-install -j$(nproc) xml && \
    docker-php-ext-install -j$(nproc) zip && \
    pecl install xdebug-${XDEBUG_VERSION} && docker-php-ext-enable xdebug && \
    rm -rf /var/lib/apt/lists/* && \
    apt-get clean

COPY .docker/php/conf.d/*.ini /usr/local/etc/php/conf.d/

# Memory limit
RUN echo "memory_limit = 1G" >> /usr/local/etc/php/php.ini

# Install composer
RUN set -xe; \
    curl -sS https://getcomposer.org/installer | php -- --filename=composer --install-dir=/usr/local/bin --version=${COMPOSER_VERSION} > /dev/null 2>&1 && \
    chmod +x /usr/local/bin/composer

WORKDIR /app

COPY composer.* ./

RUN set -xe; \
    composer install --no-autoloader --no-dev --no-interaction --no-progress --no-scripts --no-suggest --prefer-dist && \
    composer clear-cache && \
    rm -rf ./vendor/*/*/tests

COPY ./ ./
RUN composer dump-autoload
