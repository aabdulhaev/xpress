FROM php:7.2-fpm

ARG COMPOSE_PROJECT_NAME=xpress

ENV COMPOSE_PROJECT_NAME $COMPOSE_PROJECT_NAME

RUN apt-get update \
    && apt-get install -y \
        vim \
        ffmpeg \
        curl \
        wget \
        git \
        cron \
        coreutils \
        procps \
        libfreetype6-dev \
        libjpeg62-turbo-dev \
        libssh-dev \
        libpq-dev \
        libxslt-dev \
        libicu-dev \
        libpng-dev \
        libxml2-dev \
        libgearman-dev \
        libevent-dev \
    && docker-php-ext-install -j$(nproc) iconv mbstring pgsql pdo_pgsql zip bcmath soap \
    && docker-php-ext-configure gd --with-freetype-dir=/usr/include/ --with-jpeg-dir=/usr/include/ \
    && docker-php-ext-install -j$(nproc) gd \
    && docker-php-ext-configure intl \
    && docker-php-ext-install intl \
    && docker-php-ext-install xsl \
    && curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

COPY php.ini /usr/local/etc/php/conf.d/40-custom.ini
COPY docker-entrypoint.sh /usr/local/bin/

WORKDIR /var/www/"$COMPOSE_PROJECT_NAME"

ENTRYPOINT ["bash", "docker-entrypoint.sh"]

CMD ['cron','-f']
CMD ["php-fpm"]
