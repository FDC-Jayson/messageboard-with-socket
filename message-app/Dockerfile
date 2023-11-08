# Use an official PHP runtime as a parent image
FROM php:8.2-apache

# WORKDIR
WORKDIR /var/www/html

# Set environment variables
# ENV APACHE_DOCUMENT_ROOT /var/www/html/app/webroot

# Create the tmp directory and set permissions
# RUN mkdir -p /var/www/html/app/tmp
# RUN chmod -R 777 /var/www/html/app/tmp

# Enable Apache modules and configure
RUN a2enmod rewrite

# install gd
RUN apt-get update && apt-get install -y libpng-dev 
RUN apt-get install -y \
    libwebp-dev \
    libjpeg62-turbo-dev \
    libpng-dev libxpm-dev \
    libfreetype6-dev
    
# Install necessary PHP extensions
RUN docker-php-ext-install pdo pdo_mysql mysqli

# Copy the CakePHP application code into the container
COPY . /var/www/html

# expose ports
EXPOSE 443
EXPOSE 80

# Start the Apache web server
CMD ["apache2-foreground"]
