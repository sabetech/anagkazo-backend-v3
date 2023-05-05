# Use the official PHP 8 image as the base image
FROM php:8.2-apache

# Set working directory
WORKDIR /var/www/html/

# Install required packages
RUN apt-get update && apt-get install -y \
    libzip-dev \
    unzip \
    && docker-php-ext-install zip \
    && docker-php-ext-install pdo_mysql \
    && a2enmod rewrite

# Copy the application code into the container
COPY . .

# Install Composer and run "composer install" to install dependencies
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer
RUN composer install --no-interaction --no-scripts --no-plugins --no-progress --prefer-dist

# Set the ownership and permissions for the application directory
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache
RUN chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

# Expose port 80 for HTTP traffic
EXPOSE 80

# Start the Apache web server
CMD ["/usr/sbin/apache2ctl", "-D", "FOREGROUND"]
