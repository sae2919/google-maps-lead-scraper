FROM php:8.2-apache

# Set environment variables
ENV PIP_BREAK_SYSTEM_PACKAGES=1
ENV COMPOSER_ALLOW_SUPERUSER=1

# Install Linux system dependencies, Python, and Chrome for Selenium
RUN apt-get update && apt-get install -y \
    python3 \
    python3-pip \
    chromium \
    chromium-driver \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    libzip-dev \
    libicu-dev \
    zip \
    unzip \
    git \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install pdo_mysql gd zip bcmath opcache intl pcntl

# Enable Apache ModRewrite for Laravel
RUN a2enmod rewrite

# Set Apache Document Root to Laravel's public folder
ENV APACHE_DOCUMENT_ROOT /var/www/html/public
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf

# Set up working directory
WORKDIR /var/www/html

# Copy Laravel files first
COPY laravel-app/ .

# Copy scraper files to match Laravel's base_path('../scraper/main.py') structure
COPY scraper/ /var/www/scraper/

# Copy requirements.txt for pip
COPY requirements.txt ./

# Install PHP dependencies
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer
RUN composer install --no-dev --optimize-autoloader --no-interaction

# Install Python dependencies for your scraper
RUN if [ -f requirements.txt ]; then pip3 install --no-cache-dir -r requirements.txt; fi

# Set correct storage permissions for Laravel
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache

EXPOSE 80
