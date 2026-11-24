FROM php:8.2-apache

# Install system dependencies
RUN apt-get update && apt-get install -y \
    libpng-dev \
    libjpeg62-turbo-dev \
    libfreetype6-dev \
    zip \
    unzip \
    git \
    curl \
 && rm -rf /var/lib/apt/lists/*

# Install PHP extensions
RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
 && docker-php-ext-install -j$(nproc) gd pdo_mysql mysqli

# Enable Apache rewrite module
RUN a2enmod rewrite

# Suppress ServerName notice
RUN echo "ServerName localhost" >> /etc/apache2/apache2.conf

# Apache configuration to allow .htaccess
RUN echo '<Directory /var/www/html/public>\n\
    Options Indexes FollowSymLinks\n\
    AllowOverride All\n\
    Require all granted\n\
</Directory>' > /etc/apache2/conf-available/allow-override.conf \
 && a2enconf allow-override

# Set document root to /public
ENV APACHE_DOCUMENT_ROOT /var/www/html/public

RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf \
 && sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf

WORKDIR /var/www/html

COPY . .

# Install entrypoint script to /usr/local/bin so host bind-mount does not override permissions
COPY docker-entrypoint.sh /usr/local/bin/docker-entrypoint.sh
RUN chmod +x /usr/local/bin/docker-entrypoint.sh

# Permissions
RUN chown -R www-data:www-data /var/www/html \
 && chmod -R 755 /var/www/html

# Upload directories with open permissions and symlink to public/uploads
RUN mkdir -p /var/www/html/uploads/profiles \
             /var/www/html/uploads/cvs \
             /var/www/html/public/uploads \
 && chmod -R 777 /var/www/html/uploads \
 && rm -rf /var/www/html/public/uploads \
 && ln -s /var/www/html/uploads /var/www/html/public/uploads

EXPOSE 80

ENTRYPOINT ["/usr/local/bin/docker-entrypoint.sh"]
