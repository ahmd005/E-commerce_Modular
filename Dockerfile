FROM php:8.2-fpm

# تثبيت الحزم والأدوات المطلوبة لنظام Linux و Laravel
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip

# تنظيف الملفات المؤقتة
RUN apt-get clean && rm -rf /var/lib/apt/lists/*

# تثبيت ملحقات PHP الأساسية لعمل لارفل
RUN docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd

# تثبيت مدير الحزم Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# تحديد مجلد العمل داخل الحاوية
WORKDIR /var/www

# نسخ ملفات المشروع إلى داخل الحاوية
COPY . .

EXPOSE 9000
CMD ["php-fpm"]