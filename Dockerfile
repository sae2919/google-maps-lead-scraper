# ==========================================
# Stage 1: Build Frontend Assets (Vite)
# ==========================================
FROM node:20-alpine AS node-builder
WORKDIR /app
COPY laravel-app/package*.json ./
RUN npm ci
COPY laravel-app/resources ./resources
COPY laravel-app/vite.config.js ./
COPY laravel-app/tailwind.config.js ./
COPY laravel-app/postcss.config.js ./
COPY laravel-app/public ./public
RUN npm run build

# ==========================================
# Stage 2: Build Application & PHP Runtime
# ==========================================
FROM php:8.2-fpm-alpine

# Install system dependencies
RUN apk add --no-cache \
    nginx \
    supervisor \
    curl \
    libpng \
    libpng-dev \
    libjpeg-turbo \
    libjpeg-turbo-dev \
    freetype \
    freetype-dev \
    libzip \
    libzip-dev \
    icu-libs \
    icu-dev \
    bash \
    shadow

# Configure and install PHP extensions
RUN docker-php-ext-configure gd --with-freetype --with-jpeg && \
    docker-php-ext-install pdo_mysql gd zip bcmath opcache intl pcntl

# Install Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /var/www/html

# Copy project files from laravel-app
COPY laravel-app/ .

# Copy built assets from Stage 1
COPY --from=node-builder /app/public/build ./public/build

# Run composer install
ENV COMPOSER_ALLOW_SUPERUSER=1
RUN composer install --no-dev --optimize-autoloader --no-interaction

# Copy configurations from laravel-app/docker
COPY laravel-app/docker/nginx.conf /etc/nginx/http.d/default.conf
COPY laravel-app/docker/supervisord.conf /etc/supervisor/conf.d/supervisord.conf
COPY laravel-app/docker/entrypoint.sh /usr/local/bin/entrypoint.sh

# Make entrypoint script executable
RUN chmod +x /usr/local/bin/entrypoint.sh

# Set correct permissions
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache

# Expose port 80
EXPOSE 80

# Run entrypoint
ENTRYPOINT ["/usr/local/bin/entrypoint.sh"]
