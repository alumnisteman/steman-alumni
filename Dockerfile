# --- Stage 1: Build Frontend Assets (Node.js) ---
FROM node:20-alpine AS frontend-builder
WORKDIR /app
COPY package*.json ./
RUN npm install --silent
COPY . .
RUN npm run build

# --- Stage 2: Build PHP Dependencies (Composer) ---
FROM composer:latest AS composer-builder
WORKDIR /app
ENV COMPOSER_ALLOW_SUPERUSER=1
ENV COMPOSER_MEMORY_LIMIT=-1
COPY composer*.json ./
RUN composer install --no-dev --no-interaction --optimize-autoloader --ignore-platform-reqs --no-scripts

COPY . .
RUN composer dump-autoload --optimize --no-dev --no-scripts && ls -la /app/vendor/autoload.php

# --- Stage 3: Runner Stage (Final Image) ---
FROM php:8.2-fpm-alpine
LABEL maintainer="Forum Silaturahmi Alumni Steman Ternate"

# Install runtime system dependencies (including WebP + AVIF support)
RUN apk add --no-cache     libpng-dev     oniguruma-dev     libxml2-dev     libzip-dev     icu-dev     linux-headers     netcat-openbsd     unzip     libjpeg-turbo-dev     freetype-dev     libwebp-dev     libavif-dev

# Add Composer binary for runtime usage (if needed in Dev)
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Install PHP extensions (GD with WebP + AVIF support)
RUN apk add --no-cache --virtual .build-deps $PHPIZE_DEPS     && pecl install redis     && docker-php-ext-enable redis     && docker-php-ext-configure gd --with-freetype --with-jpeg --with-webp --with-avif     && docker-php-ext-install     pdo_mysql     mbstring     exif     pcntl     bcmath     gd     zip     intl     sockets     opcache     && apk del .build-deps

# Setting up application directory
WORKDIR /var/www

# Copy PHP config
COPY docker/php/opcache.ini /usr/local/etc/php/conf.d/opcache.ini
COPY docker/php/local.ini /usr/local/etc/php/conf.d/local.ini

# Copy Application files first
COPY . /var/www/

# Copy PHP dependencies (this ensures vendor from builder overrides anything else)
COPY --from=composer-builder /app/vendor /var/www/vendor

# Copy Frontend assets
COPY --from=frontend-builder /app/public/build /var/www/public/build

# Copy custom entrypoint
COPY docker/docker-entrypoint.sh /usr/local/bin/docker-entrypoint.sh
RUN chmod +x /usr/local/bin/docker-entrypoint.sh

# Optimize Permissions
RUN chmod -R 755 /var/www     && chown -R www-data:www-data /var/www/storage /var/www/bootstrap/cache     && chmod -R 775 /var/www/storage /var/www/bootstrap/cache

# Environment configuration
ENV APP_ENV=production
ENV APP_DEBUG=false

# Expose PHP-FPM port
EXPOSE 9000

# Start Container via Entrypoint (Respects CMD)
ENTRYPOINT ["docker-entrypoint.sh"]
CMD ["php-fpm"]
