FROM php:8.1-apache

# Enable mysqli and pdo_mysql
RUN apt-get update && apt-get install -y libzip-dev zip libpng-dev && \
    docker-php-ext-install mysqli pdo pdo_mysql && \
    apt-get clean && rm -rf /var/lib/apt/lists/*

ENV APACHE_DOCUMENT_ROOT=/var/www/html/public

# Copy app files
COPY . /var/www/html/

RUN sed -ri -e 's!/var/www/html!/var/www/html/public!g' /etc/apache2/sites-available/*.conf && \
    sed -ri -e 's!/var/www/html!/var/www/html/public!g' /etc/apache2/apache2.conf

# Set working directory and expose port
WORKDIR /var/www/html/public
EXPOSE 80

# Ensure uploads directory exists
RUN mkdir -p /var/www/html/public/uploads && chown -R www-data:www-data /var/www/html/public/uploads

# Enable rewrite if needed
RUN a2enmod rewrite
