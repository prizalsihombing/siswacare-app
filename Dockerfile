# Gunakan PHP image resmi yang menyertakan Composer dan ekstensi yang dibutuhkan
FROM php:8.2-fpm

# Install dependencies sistem, ekstensi PHP, dan Node.js (untuk npm run build)
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip \
    && curl -fsSL https://deb.nodesource.com/setup_20.x | bash - \
    && apt-get install -y nodejs \
    && docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd

# Get latest Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /var/www

# Copy seluruh file project ke dalam container
COPY . /var/www

# Install PHP dependencies
RUN composer install --no-dev --optimize-autoloader

# Install Node.js dependencies dan Build aset Vite (manifest.json akan digenerate di sini!)
RUN npm install && npm run build

# Berikan izin akses pada folder storage dan bootstrap/cache
RUN chown -R www-data:www-data /var/www/storage /var/www/bootstrap/cache

# Expose port untuk web server (Railway biasanya menggunakan port 80 atau variabel $PORT)
EXPOSE 8080

# Jalankan script startup atau built-in server PHP/Artisan
CMD php artisan serve --host=0.0.0.0 --port=${PORT:-8080}