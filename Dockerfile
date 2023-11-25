# Use PHP 8.1
FROM php:8.1-fpm

# Install dependencies
RUN apt-get update && apt-get install -y \
    libzip-dev \
    unzip \
    libonig-dev \
    libxml2-dev \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    libwebp-dev

# Install PHP extensions
RUN docker-php-ext-install pdo_mysql zip mbstring exif pcntl bcmath gd

# Install Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Set working directory
WORKDIR /app

# Copy project files
COPY . .

# Set permissions (make sure to adjust permissions accordingly)
RUN chown -R www-data:www-data /app
RUN chmod -R 755 storage bootstrap/cache

# Update Composer and install dependencies
RUN composer self-update --2
RUN composer clear-cache
RUN composer install --no-dev --no-interaction --prefer-dist --optimize-autoloader --verbose

# Copy environment file
COPY .env.example .env

# Generate application key
RUN php artisan key:generate

# Expose port 8000
EXPOSE 8000

# Run PHP-FPM
CMD ["php-fpm"]
