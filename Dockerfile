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
RUN docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd sqlite3 pdo_sqlite

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

# Salin seluruh file project
COPY . /var/www/html

# Buat file .env dari .env.example jika belum ada
RUN [ -f .env ] || cp .env.example .env

# Install vendor dependencies
RUN composer install --no-dev --optimize-autoloader --no-interaction

# Berikan izin akses folder storage, database, dan bootstrap cache
RUN mkdir -p /var/www/html/database && touch /var/www/html/database/database.sqlite
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/database /var/www/html/bootstrap/cache
RUN chmod -R 775 /var/www/html/storage /var/www/html/database /var/www/html/bootstrap/cache

EXPOSE 80

# Jalankan key:generate, migrasi database, dan server Laravel
CMD php artisan key:generate --force && php artisan config:clear && php artisan migrate --force && php artisan serve --host=0.0.0.0 --port=80