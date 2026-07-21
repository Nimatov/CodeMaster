FROM php:8.2-apache

# Устанавливаем системные зависимости и расширения для базы данных (PostgreSQL)
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    libpq-dev \
    zip \
    unzip

# Очищаем кэш
RUN apt-get clean && rm -rf /var/lib/apt/lists/*

# Устанавливаем расширения PHP для Laravel и PostgreSQL
RUN docker-php-ext-install pdo pdo_pgsql pgsql mbstring exif pcntl bcmath gd

# Включаем модуль rewrite для Apache
RUN a2enmod rewrite

# Устанавливаем Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Копируем файлы проекта в контейнер
WORKDIR /var/www/html
COPY . .

# Даем права на папки хранения и кэша Laravel
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache

# Меняем корневую папку Apache на public (как требует Laravel)
ENV APACHE_DOCUMENT_ROOT /var/www/html/public
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /apache2.conf /etc/apache2/sites-available/*.conf
RUN sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf

# Устанавливаем зависимости через composer
RUN composer install --no-dev --optimize-autoloader

EXPOSE 80