FROM php:8.3-apache

# Install the necessary PHP extensions for MySQL
RUN docker-php-ext-install mysqli

# Enable Apache mod_rewrite
RUN a2enmod rewrite

# Copy entire codebase into the container
COPY . /var/www/html/

# Ensure Apache has the right permissions to read the files
RUN chown -R www-data:www-data /var/www/html/