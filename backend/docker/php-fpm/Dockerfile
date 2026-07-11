FROM php:8.5-fpm

ARG INSTALL_XDEBUG=false

WORKDIR /app

RUN apt-get update && apt-get install -y \
    bash \
    curl \
    zip \
    unzip \
    libpng-dev \
    libwebp-dev \
    libjpeg62-turbo-dev \
    libfreetype6-dev \
    libzip-dev \
    libpq-dev \
    linux-headers-amd64 \
    supervisor \
    && docker-php-ext-configure gd --with-freetype --with-jpeg --with-webp \
    && docker-php-ext-install \
        zip \
        pdo_pgsql \
        pgsql \
        gd \
        bcmath \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

RUN curl -sS https://getcomposer.org/installer | php -- \
    --install-dir=/usr/local/bin \
    --filename=composer \
    --quiet

RUN if [ "$INSTALL_XDEBUG" = "true" ]; then \
    pecl install xdebug && docker-php-ext-enable xdebug; \
    fi

COPY ./docker/php-fpm/xdebug.ini "${PHP_INI_DIR}/conf.d"

COPY ./docker/php/conf.d "${PHP_INI_DIR}/conf.d"

COPY ./docker/wait-for-it.sh /usr/local/bin/wait-for-it
RUN chmod 555 /usr/local/bin/wait-for-it

COPY ./docker/supervisor/supervisord.conf /etc/supervisor/conf.d/supervisord.conf

RUN mkdir -p /var/log/supervisor && chmod 777 /var/log/supervisor

COPY ./docker/php-fpm/start.sh /usr/local/bin/start.sh
RUN chmod +x /usr/local/bin/start.sh

EXPOSE 9000

CMD ["/usr/local/bin/start.sh"]
