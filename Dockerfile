FROM php:7.1-apache
RUN a2enmod rewrite

# Setup the OS for PHP
RUN docker-php-source extract \
    && apt-get update \
    && apt-get install --no-install-recommends -y \
    libxml2-dev \
    libfreetype6-dev \
    libjpeg62-turbo-dev \
    libmcrypt-dev \
    libpng-dev \
    libicu-dev \
    libpq-dev \
    && apt-get clean \
    && rm -rf /tmp/*

# Setup PHP extensions
RUN docker-php-ext-configure opcache \
    && docker-php-ext-configure calendar \
    && docker-php-ext-configure exif \
    && docker-php-ext-configure gd --with-freetype-dir=/usr/include/ --with-jpeg-dir=/usr/include/ \
    && docker-php-ext-configure fileinfo \
    && docker-php-ext-configure gettext \
    && docker-php-ext-configure mysqli \
    && docker-php-ext-configure pdo \
    && docker-php-ext-configure pdo_mysql \
    && docker-php-ext-configure json \
    && docker-php-ext-configure session \
    && docker-php-ext-configure ctype \
    && docker-php-ext-configure tokenizer \
    && docker-php-ext-configure simplexml \
    && docker-php-ext-configure dom \
    && docker-php-ext-configure mbstring \
    && docker-php-ext-configure zip \
    && docker-php-ext-configure xml \
    && docker-php-ext-configure intl \
    && docker-php-source delete \\
    && docker-php-ext-install \
    opcache \
    calendar \
    exif \
    gd \
    fileinfo \
    gettext \
    mysqli \
    pdo \
    pdo_mysql \
    json \
    session \
    ctype \
    tokenizer \
    simplexml \
    dom \
    mbstring \
    zip \
    xml \
    intl

# Get composer
RUN curl -sS https://getcomposer.org/installer | php \
    && mv composer.phar /usr/local/bin/composer

# Manually set up the apache environment variables
ENV APACHE_RUN_USER www-data
ENV APACHE_RUN_GROUP www-data
ENV APACHE_LOG_DIR /var/log/apache2
ENV APACHE_LOCK_DIR /var/lock/apache2
ENV APACHE_PID_FILE /var/run/apache2.pid

# Update the default apache site with the config we created.
ADD conf/apache-config.conf /etc/apache2/sites-enabled/000-default.conf
ADD conf/php-dev.ini /usr/local/etc/php/php.ini
