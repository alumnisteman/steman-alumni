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
# Set composer environment to be more resilient
ENV COMPOSER_ALLOW_SUPERUSER=1
ENV COMPOSER_MEMORY_LIMIT=-1
COPY composer*.json ./
# Run composer with extreme parameters to avoid timeouts/memory issues
RUN composer install --no-dev --no-interaction --optimize-autoloader --ignore-platform-reqs --no-scripts || \
    (composer config -g repo.packagist composer https://packagist.phpcomposer.com && \
     composer install --no-dev --no-interaction --optimize-autoloader --ignore-platform-reqs --no-scripts)

COPY . .
RUN composer dump-autoload --optimize --no-dev

# --- Stage 3: Runner Stage (Final Image) ---
FROM php:8.2-fpm-alpine
LABEL maintainer="Ikatan Alumni STEMAN"

# Install runtime system dependencies
RUN apk add --no-cache \
    libpng-dev \
    oniguruma-dev \
    libxml2-dev \
    libzip-dev \
    icu-dev \
    linux-headers \
    netcat-openbsd \
    unzip \
    libjpeg-turbo-dev \
    freetype-dev

# Add Composer binary for runtime usage (if needed in Dev)
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Install PHP extensions
RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install \
    pdo_mysql \
    mbstring \
    exif \
    pcntl \
    bcmath \
    gd \
    zip \
    intl \
    sockets \
    opcache

# Setting up application directory
WORKDIR /var/www

# Copy PHP config
COPY docker/php/opcache.ini /usr/local/etc/php/conf.d/opcache.ini

# Copy PHP dependencies
COPY --from=composer-builder /app/vendor /var/www/vendor
COPY . /var/www/

# Copy Frontend assets
COPY --from=frontend-builder /app/public/build /var/www/public/build
COPY --from=frontend-builder /app/public/manifest.json /var/www/public/manifest.json

# Copy custom entrypoint
COPY docker/docker-entrypoint.sh /usr/local/bin/docker-entrypoint.sh
RUN chmod +x /usr/local/bin/docker-entrypoint.sh

# Optimize Permissions
RUN chown -R www-data:www-data /var/www \
    && chmod -R 755 /var/www \
    && chmod -R 775 /var/www/storage \
    && chmod -R 775 /var/www/bootstrap/cache

# Build-time Laravel Optimization (Production ready)
# Note: Use dummy env to avoid DB connection during build
RUN php artisan config:cache \
    && php artisan route:cache \
    && php artisan view:cache

# Environment configuration
ENV APP_ENV=production
ENV APP_DEBUG=false

# Expose PHP-FPM port
EXPOSE 9000

# Start Container via Entrypoint (Respects CMD)
ENTRYPOINT ["docker-entrypoint.sh"]
CMD ["php-fpm"]
