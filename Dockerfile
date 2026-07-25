FROM php:8.2-cli

# Install dependencies sistem
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip

# Bersihkan cache
RUN apt-get clean && rm -rf /var/lib/apt/lists/*

# Install ekstensi PHP untuk Laravel
RUN docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

# Salin file konfigurasi composer terlebih dahulu untuk caching dependencies
COPY composer.json composer.lock ./

# Install vendor dependencies
RUN composer install --no-dev --optimize-autoloader --no-interaction

# Salin seluruh sisa file project
COPY . /var/www/html

RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache

EXPOSE 8080

# Jalankan server bawaan Laravel pada port 8080 (atau port 80 jika diinginkan)
CMD php artisan serve --host=0.0.0.0 --port=8080