ARG PLATFORM_IMAGE=php:8.3-apache
FROM ${PLATFORM_IMAGE}

RUN docker-php-ext-install mysqli pdo pdo_mysql
RUN a2enmod rewrite

COPY docker/platform/000-default.conf /etc/apache2/sites-available/000-default.conf

WORKDIR /var/www/html
