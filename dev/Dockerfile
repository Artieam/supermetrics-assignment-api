FROM php:7.4-fpm

WORKDIR /var/www

RUN apt-get update && apt-get install -y \
        curl \
        wget \
        git
    
# Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

WORKDIR /var/www

CMD ["php-fpm"]
