FROM yiisoftware/yii2-php:8.1-fpm-nginx
WORKDIR /app
COPY . .

RUN mkdir -p uploads
RUN chmod -R 775 uploads assets runtime
RUN composer install --optimize-autoloader --no-dev
RUN composer clear-cache
