# Use the official PHP image with Apache
FROM php:8.2-apache

# Set the working directory
WORKDIR /var/www/html

# Install necessary PHP extensions and SQLite
RUN apt-get update && \
    apt-get install -y libsqlite3-dev && \
    docker-php-ext-install pdo pdo_sqlite

RUN a2enmod rewrite

# Copy the application code to the container
COPY public/index.php /var/www/html/
COPY src/ /var/www/src/
COPY database.sqlite /var/www/
COPY .htaccess /var/www/

# Grant write permissions to the SQLite database file
RUN chown -R www-data:www-data /var/www/html
RUN chmod -R 755 /var/www/html
RUN chmod -R 777 /var/www/database.sqlite

# Expose port 80
EXPOSE 80

# Start Apache server in the foreground
CMD ["apache2-foreground"]