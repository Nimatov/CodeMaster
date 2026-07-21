FROM php:8.2-fpm-alpine

# Устанавливаем все расширения
RUN apk add --no-cache postgresql-dev && \
    docker-php-ext-install pdo pdo_mysql pdo_pgsql

# Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

COPY . .

# Установка зависимостей
RUN composer install --no-interaction --no-progress --no-dev --optimize-autoloader

# Создаем .env
RUN cp .env.example .env || true

# Генерация ключа
RUN php artisan key:generate

# Кеширование
RUN php artisan config:cache

# Права
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache
RUN chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

EXPOSE 8080

CMD ["php", "artisan", "serve", "--host=0.0.0.0", "--port=8080"]