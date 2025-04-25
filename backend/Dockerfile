FROM php:8.1-apache

WORKDIR /var/www/html

# Install dependencies
RUN apt-get update && apt-get install -y \
    libzip-dev \
    unzip \
    && docker-php-ext-install zip pdo pdo_mysql

# Enable Apache rewrite module
RUN a2enmod rewrite

# Copy composer.json and install dependencies
COPY composer.json composer.lock ./
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer
RUN composer install --no-scripts --no-autoloader

# Copy source code
COPY . .

# Generate optimized autoload files
RUN composer dump-autoload --optimize

# Set permissions
RUN chown -R www-data:www-data /var/www/html

# Configure Apache
COPY apache-config.conf /etc/apache2/sites-available/000-default.conf

# Set environment variables
ENV PORT=8001

# Expose the port the app runs on
EXPOSE 8001

# Command to start Apache in foreground
CMD ["apache2-foreground"] 