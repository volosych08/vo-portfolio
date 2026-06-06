FROM php:8.4-cli

WORKDIR /var/www/html

RUN apt-get update \
    && apt-get install -y git unzip libsqlite3-dev default-libmysqlclient-dev \
    && docker-php-ext-install pdo pdo_sqlite pdo_mysql \
    && rm -rf /var/lib/apt/lists/*

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

COPY composer.json ./

RUN composer install --no-interaction --prefer-dist

COPY . .

RUN mkdir -p default-uploads \
    && cp -R public/uploads/. default-uploads/ 2>/dev/null || true

RUN mkdir -p storage public/uploads/blog public/uploads/projects \
    && chmod -R 775 storage public/uploads

COPY docker/php.ini /usr/local/etc/php/conf.d/custom.ini

EXPOSE 8888

CMD ["sh", "-c", "mkdir -p public/uploads/blog public/uploads/projects && cp -Rn default-uploads/. public/uploads/ 2>/dev/null || true; php -S 0.0.0.0:8888 -t public"]
