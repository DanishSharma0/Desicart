FROM php:8.2-fpm AS base

# Install system dependencies
RUN apt-get update && apt-get install -y \
    git \
    unzip \
    libzip-dev \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    ca-certificates \
    curl \
    nginx \
    supervisor \
 && rm -rf /var/lib/apt/lists/*

RUN docker-php-ext-install pdo pdo_mysql zip gd mbstring xml

# Install composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

## Production build stage: install PHP deps and build vendor
FROM base AS vendor
COPY composer.json composer.lock ./
RUN composer install --no-dev --optimize-autoloader --no-interaction --prefer-dist --no-scripts

COPY . /var/www/html

RUN composer dump-autoload --optimize

## Node build stage for frontend assets
FROM node:18-alpine AS node_builder
WORKDIR /app
COPY package.json package-lock.json ./
COPY resources resources
COPY vite.config.js postcss.config.js tailwind.config.js ./
RUN npm ci
RUN npm run build

## Final image
FROM base AS final
WORKDIR /var/www/html

# Copy application files
COPY --from=vendor /var/www/html /var/www/html

# Copy composer vendor
COPY --from=vendor /var/www/html/vendor /var/www/html/vendor

# Copy built assets from node stage
COPY --from=node_builder /app/public/build /var/www/html/public/build

# Set permissions
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache

# Copy nginx config and entrypoint
COPY docker/nginx/default.conf /etc/nginx/sites-available/default
RUN ln -sf /etc/nginx/sites-available/default /etc/nginx/sites-enabled/default
COPY docker/docker-entrypoint.sh /usr/local/bin/docker-entrypoint.sh
RUN chmod +x /usr/local/bin/docker-entrypoint.sh

EXPOSE 80

ENV APP_ENV=production

CMD ["/usr/local/bin/docker-entrypoint.sh"]
