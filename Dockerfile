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

# Salin seluruh file project
COPY . /var/www/html

# Buat file .env dari .env.example jika belum ada
RUN [ -f .env ] || cp .env.example .env

# Install vendor dependencies
RUN composer install --no-dev --optimize-autoloader --no-interaction

# Berikan izin akses folder storage dan bootstrap cache
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache

EXPOSE 80

# Jalankan perintah key:generate dan server Laravel
CMD php artisan key:generate --force && php artisan config:clear && php artisan serve --host=0.0.0.0 --port=80