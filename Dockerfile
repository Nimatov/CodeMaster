FROM php:8.0-apache

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

# Копируем файлы проекта (включая папку vendor)
WORKDIR /var/www/html
COPY . .

# Создаем .env из примера, если его нет
RUN [ -f .env ] || cp .env.example .env

# Очищаем кэш конфигурации и приложения, чтобы подтянулись переменные с Render
RUN php artisan config:clear
RUN php artisan cache:clear

# Даем права на папки хранения и кэша Laravel
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache
RUN chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

# Меняем стандартный корень Apache на папку public проекта Laravel
RUN sed -ri -e 's!/var/www/html!/var/www/html/public!g' /etc/apache2/sites-available/*.conf
RUN sed -ri -e 's!/var/www/!/var/www/html/public!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf

EXPOSE 80

# Автомиграция базы данных и запуск Apache
CMD php artisan migrate --force && apache2-foreground