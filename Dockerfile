FROM php:8.2-apache

# Install ekstensi dan dependencies sistem yang dibutuhkan Laravel
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip

# Bersihkan cache untuk memperkecil ukuran image
RUN apt-get clean && rm -rf /var/lib/apt/lists/*

# Install ekstensi PHP yang sering dipakai Laravel (Database MySQL, dll)
RUN docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd

# Menonaktifkan modul MPM lain dan mengaktifkan prefork secara bersih
RUN rm -f /etc/apache2/mods-enabled/mpm_event.load /etc/apache2/mods-enabled/mpm_event.conf \
    && rm -f /etc/apache2/mods-enabled/mpm_worker.load /etc/apache2/mods-enabled/mpm_worker.conf \
    && rm -f /etc/apache2/mods-enabled/mpm_prefork.load /etc/apache2/mods-enabled/mpm_prefork.conf \
    && ln -s /etc/apache2/mods-available/mpm_prefork.load /etc/apache2/mods-enabled/ \
    && ln -s /etc/apache2/mods-available/mpm_prefork.conf /etc/apache2/mods-enabled/

# Salin Composer resmi ke dalam container Docker
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Atur direktori kerja utama di dalam container
WORKDIR /var/www/html

# Salin seluruh file project dari komputer lokal ke dalam container
COPY . /var/www/html

# Berikan izin akses folder storage dan bootstrap cache agar Laravel bisa menulis log/session
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache

# Buka port 80 untuk web server Apache
EXPOSE 80