FROM php:8.2-fpm

RUN apt-get update && apt-get install -y \
    git \
    unzip \
    libxml2-dev \
    libxslt-dev \
    libicu-dev \
    libzip-dev \
    libmemcached-dev \
    zlib1g-dev \
    libssl-dev \
    libonig-dev \
    supervisor \
    && docker-php-ext-install \
        intl \
        xsl \
        zip \
        pdo_mysql \
        opcache

RUN pecl install memcached && docker-php-ext-enable memcached
RUN usermod -u 1000 www-data

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

WORKDIR /var/www/app
