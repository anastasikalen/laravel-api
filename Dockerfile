# Используем официальный образ PHP с поддержкой FPM и расширений
FROM php:8.1-fpm

# Устанавливаем необходимые системные зависимости
RUN apt-get update && apt-get install -y \
    build-essential \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    locales \
    zip \
    jpegoptim optipng pngquant gifsicle \
    vim \
    unzip \
    git \
    curl \
    libzip-dev \
    libonig-dev \ 
    libxml2-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd zip \
    && pecl install redis \
    && docker-php-ext-enable redis

# Устанавливаем зависимости PHP и расширения
RUN docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd

# Устанавливаем Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Копируем локальные файлы в контейнер
COPY . /var/www

# Устанавливаем рабочую директорию
WORKDIR /var/www

# Устанавливаем права для хранения кеша
RUN chown -R www-data:www-data /var/www/storage /var/www/bootstrap/cache

# Установка Laravel Sanctum
RUN composer require laravel/sanctum

# Expose port 9000 and start php-fpm server
EXPOSE 9000
CMD ["php-fpm"]
