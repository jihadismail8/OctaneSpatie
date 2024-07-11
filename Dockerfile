FROM composer:2.7.7  AS Base
WORKDIR /var/www/html


ENV MAKEFLAGS=" -j 4"
RUN apk add --no-cache --virtual .build-deps $PHPIZE_DEPS \
    && curl -sSL https://github.com/mlocati/docker-php-extension-installer/releases/latest/download/install-php-extensions -o - | sh -s \
    grpc-1.64.1 \
    pdo_mysql \
    pcntl \
    gd \
    intl \
    zip \
    opcache\
    pgsql\
    pdo_pgsql \
    protobuf \
    bcmath \
    ldap \
    redis \
    amqp \
    ssh2 \
    mcrypt \
    snmp \
    memcached \
    mongodb \
    sockets \
    && pecl install openswoole && docker-php-ext-enable openswoole \
    && apk del g++ \
    && apk del make \
    && apk del curl \
    && apk del gcc \
    && rm /usr/src/php.tar.xz \
    && apk del .build-deps
RUN \
    set -eux \
    \
    && find "$(php-config --extension-dir)" -name '*.so' -type f -exec strip --strip-all {} \;

# WORKDIR /var/www/html
COPY . /var/www/html


RUN composer install
RUN composer dumpautoload --optimize
RUN ./vendor/bin/rr get-binary
RUN php artisan migrate
RUN php artisan migrate:fresh
RUN php artisan db:seed --class=RolesAndPermissionsSeeder
RUN php artisan vendor:publish --tag=telescope-assets

RUN apk update && apk add supervisor && apk add postgresql-client
RUN mkdir -p /var/log/supervisor
COPY supervisord.conf /etc/supervisord.conf


#for dev
RUN apk add --update npm && npm install && npm install --save-dev chokidar
EXPOSE 8081 81 23 23/udp 9001 8091 8091/udp 8082 8082/udp
RUN chmod -R 777 ./public
ENTRYPOINT ["/usr/bin/supervisord", "-c", "/etc/supervisord.conf"]
