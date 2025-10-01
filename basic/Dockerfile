FROM yiisoftware/yii2-php:7.4-fpm
LABEL maintainer="hexman84 <hexman@live.ru>"
# RUN apt-get update && apt-get install -y \
#         libmcrypt-dev \
#         && apt-get install -y libpq-dev libxml2-dev \
#         && docker-php-ext-install -j$(nproc) bcmath iconv mysqli pdo_mysql mbstring soap

RUN pecl update-channels pecl.php.net
# RUN pecl install mcrypt-1.0.1 && docker-php-ext-enable mcrypt
# RUN pecl uninstall xdebug \
#     && pecl install xdebug-2.9.8 \
#     && docker-php-ext-enable xdebug

# Install Composer.
RUN curl -sS https://getcomposer.org/installer | php -- \
        --filename=composer.phar \
        --version=2.2.9 \
        --install-dir=/usr/local/bin && \
    composer clear-cache

RUN chmod +x /usr/local/bin/composer.phar