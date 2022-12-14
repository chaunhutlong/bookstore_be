FROM php:8.1-fpm

# Install system dependencies
RUN apt-get update && apt-get install -y git

# Install PHP extensions
RUN docker-php-ext-install pdo_mysql

# Get latest Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /var/www

RUN chown -R www-data:www-data /var/www

RUN chmod -R 755 /var/www/storage