FROM php:7.3-apache

RUN apt update
RUN docker-php-ext-install mysqli pdo pdo_mysql

WORKDIR /var/www/html/
COPY ./ .
# config permission
RUN chown -R root:www-data /var/www/html
RUN chmod 750 /var/www/html

# allow rewrite
RUN a2enmod rewrite
# Cho phép override cấu hình Apache từ .htaccess
RUN sed -ri -e 's!/etc/apache2/sites-available!${APACHE_CONFDIR}!g' /etc/apache2/apache2.conf && \
sed -ri -e 's!/etc/apache2/mods-available!${APACHE_CONFDIR}!g' /etc/apache2/apache2.conf

RUN find . -type f -exec chmod 640 {} \;
RUN find . -type d -exec chmod 750 {} \;

RUN chmod +t -R /var/www/html/

RUN service apache2 restart
