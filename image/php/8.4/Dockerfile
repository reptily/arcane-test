FROM php:8.4-fpm

RUN apt-get update -y && apt-get install -y \
git \
curl \
libz-dev \
libmemcached-dev \
libcurl4-openssl-dev \
libxml2-dev \
libssl-dev \
libpng-dev \
libonig-dev \
libpq-dev \
librabbitmq-dev \
libzip-dev \
libevent-dev \
librdkafka-dev \
ffmpeg \
build-essential

# Kafka
RUN pecl install rdkafka-6.0.5 && docker-php-ext-enable rdkafka

# Xdebug
RUN pecl install xdebug-3.4.1 && docker-php-ext-enable xdebug

# Redis
RUN pecl install redis-6.1.0  && docker-php-ext-enable redis

# MySQL
RUN docker-php-ext-install mysqli
RUN docker-php-ext-install pdo_mysql

# Composer
RUN curl -sS https://getcomposer.org/installer | php -- \
        --filename=composer \
        --install-dir=/usr/local/bin && \
        echo "alias composer='composer'" >> /root/.bashrc

CMD ["php-fpm", "-F"]

WORKDIR /var/www

EXPOSE 9000
