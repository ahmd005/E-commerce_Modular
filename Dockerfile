FROM php:8.2-fpm

# تحديد مجلد العمل داخل الحاوية أولاً
WORKDIR /var/www

# تثبيت الحزم والأدوات المطلوبة لنظام Linux و Laravel وتجنب التكرار
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip \
    zlib1g-dev \
    libzip-dev \
    autoconf \
    g++ \
    make \
    pkg-config \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# تثبيت ملحقات PHP الأساسية لعمل لارفل
RUN docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd zip

# تثبيت ملحقات gRPC و Protobuf بإصدارات مستقرّة لتجنب مشاكل التوافق والتنزيل
RUN pecl install grpc protobuf \
    && docker-php-ext-enable grpc protobuf

# تثبيت مدير الحزم Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# نسخ ملفات المشروع إلى داخل الحاوية
COPY . .

EXPOSE 9000
CMD ["php-fpm"]