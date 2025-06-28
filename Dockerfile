# syntax=docker/dockerfile:1

# --- Build Stage ---
FROM php:8.2-fpm AS build

# Install system dependencies and PHP extensions
RUN apt-get update && apt-get install -y --no-install-recommends \
    libpq-dev \
    libonig-dev \
    libzip-dev \
    libxml2-dev \
    libcurl4-openssl-dev \
    libicu-dev \
    unzip \
    git \
    && docker-php-ext-install -j$(nproc) \
        pdo_mysql \
        pdo_pgsql \
        pgsql \
        intl \
        zip \
        bcmath \
        soap \
    && pecl install redis \
    && docker-php-ext-enable redis \
    && apt-get clean && rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/*

WORKDIR /var/www

# Install Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Copy only composer files for dependency install (improves build cache)
COPY --link composer.json composer.lock* ./

# Install PHP dependencies
RUN composer install --no-dev --optimize-autoloader --no-interaction --no-progress --prefer-dist

# Copy application code (excluding .env, .git, etc. via .dockerignore)
COPY --link . .

# --- Production Stage ---
FROM php:8.2-fpm AS final

# Create non-root user
RUN addgroup --system appgroup && adduser --system --ingroup appgroup appuser

# Install runtime dependencies
RUN apt-get update && apt-get install -y --no-install-recommends \
    libpq-dev \
    libonig-dev \
    libzip-dev \
    libxml2-dev \
    libcurl4-openssl-dev \
    libicu-dev \
    && apt-get clean && rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/*

WORKDIR /var/www

# Copy built app from build stage
COPY --from=build --link /var/www /var/www

# Set permissions for storage and bootstrap/cache
RUN chown -R appuser:appgroup storage bootstrap/cache && \
    chmod -R 775 storage bootstrap/cache

USER appuser

EXPOSE 9000
CMD ["php-fpm"]
