FROM php:7.2-fpm

# Install essentials
RUN buildDeps="curl libpq-dev libzip-dev libicu-dev libjpeg62-turbo-dev libfreetype6-dev libmagickwand-6.q16-dev libpng-dev" && \
    apt-get update --fix-missing && \
    apt-get install -y $buildDeps --no-install-recommends && \
    apt-get clean && \
    rm -rf /var/lib/apt/lists/*

# Install composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Install PHP modules
RUN docker-php-ext-configure gd --with-freetype-dir=/usr/include/ --with-jpeg-dir=/usr/include/ && \
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

CMD ["php-fpm"]
