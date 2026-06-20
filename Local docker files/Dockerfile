# Use the official PHP image with Apache
FROM php:8.3-apache

# Install the necessary PHP extensions for MySQL
RUN docker-php-ext-install mysqli pdo pdo_mysql

# Enable Apache mod_rewrite (often needed for routing)
RUN a2enmod rewrite