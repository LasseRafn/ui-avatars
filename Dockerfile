FROM php:8.4-fpm

# Install system dependencies and Nginx
RUN apt-get update && apt-get install -y \
    nginx \
    git \
    unzip \
    libfreetype6-dev \
    libjpeg62-turbo-dev \
    libpng-dev \
    libwebp-dev \
    libxpm-dev \
    libgd-dev \
    cron \
    supervisor \
    && docker-php-ext-configure gd \
        --with-freetype \
        --with-jpeg \
        --with-webp \
        --with-xpm \
    && docker-php-ext-install gd \
    && rm -rf /var/lib/apt/lists/*

# Install Composer globally
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /var/www/html

# Copy application source code
COPY . /var/www/html

RUN mkdir -p /var/www/html/cache \
    && chown -R www-data:www-data cache \
    && chmod -R 755 cache \
    && composer install --no-dev --optimize-autoloader \
    && rm /etc/nginx/sites-enabled/default

COPY ./.docker/nginx.conf /etc/nginx/nginx.conf
COPY ./.docker/supervisord.conf /etc/supervisor/conf.d/supervisord.conf

# Set up cron job to clean cache
COPY ./.docker/cleanup_cron /tmp/cleanup_cron
RUN crontab -u www-data /tmp/cleanup_cron && rm /tmp/cleanup_cron \
    && touch /var/log/cron_cleanup.log \
    && chown www-data:www-data /var/log/cron_cleanup.log


RUN chown -R www-data:www-data /var/www/html

EXPOSE 8678

CMD ["/usr/bin/supervisord"]
