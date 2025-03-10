# Use PHP 8.4 with required extensions
FROM php:8.4-cli

# Install dependencies
RUN apt-get update && apt-get install -y \
    libsqlite3-dev \
    libsodium-dev \
    unzip \
    curl \
    git \
    && docker-php-ext-install pdo pdo_sqlite sodium

# Install Redis extension
RUN pecl install redis && docker-php-ext-enable redis

# Install Node.js for frontend
RUN curl -fsSL https://deb.nodesource.com/setup_18.x | bash - && \
    apt-get install -y nodejs

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /var/www

# Copy Laravel project files
COPY . .

# Install Laravel dependencies
RUN composer install --no-dev --no-interaction --prefer-dist && \
    npm install && npm run build

# Expose necessary ports
EXPOSE 8000 5173

# Command to start Laravel & Vite
CMD ["sh", "-c", "php artisan serve --host=0.0.0.0 --port=8000 & npm run dev"]
