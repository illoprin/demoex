FROM php:8.2-fpm

# Установка необходимых PHP расширений
RUN docker-php-ext-install mysqli pdo pdo_mysql

# Установка дополнительных пакетов если нужно
RUN apt-get update && apt-get install -y \
    zip \
    unzip \
    && rm -rf /var/lib/apt/lists/*

# Рабочая директория
WORKDIR /var/www/html

# Копирование PHP файлов
COPY ./site /var/www/html/

# Права доступа
RUN chown -R www-data:www-data /var/www/html