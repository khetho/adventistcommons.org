FROM php:7.4-fpm

# Install essentials
RUN buildDeps="curl libpq-dev libzip-dev libicu-dev libjpeg62-turbo-dev libfreetype6-dev libmagickwand-6.q16-dev libpng-dev" && \
    apt-get update --fix-missing && \
    apt-get install -y $buildDeps --no-install-recommends && \
    apt-get clean && \
    rm -rf /var/lib/apt/lists/*

# Install PHP modules
RUN docker-php-ext-configure gd --with-freetype --with-jpeg && \
    docker-php-ext-install \
        opcache \
        pdo \
        pdo_pgsql \
        pdo_mysql \
        mysqli \
        pgsql \
        sockets \
        intl\
        zip\
        gd

RUN usermod -u 1000 www-data

COPY --from=composer /usr/bin/composer /usr/bin/composer

CMD ["php-fpm"]
