FROM php:8.2-cli
COPY . /var/www/html/
WORKDIR /var/www/html
RUN docker-php-ext-install mysqli pdo pdo_mysql
CMD ["php", "-S", "0.0.0.0:80"]
EXPOSE 80