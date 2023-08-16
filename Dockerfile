FROM php:8.1-fpm-alpine


RUN set -ex \
    && apk --no-cache add postgresql-dev yarn\
    && docker-php-ext-install pdo pdo_pgsql exif

RUN curl -sSL https://github.com/mlocati/docker-php-extension-installer/releases/latest/download/install-php-extensions -o - | sh -s \
      gd xdebug exif


RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer


WORKDIR /var/www/html
