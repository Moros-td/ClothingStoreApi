FROM php:7.3-apache

RUN apt update
RUN docker-php-ext-install mysqli pdo pdo_mysql

WORKDIR /var/www/html/
COPY ./ .
# config permission
RUN chown -R root:www-data /var/www/html
RUN chmod 750 /var/www/html

# Tạo thư mục /tmp/uploadFiles
# RUN mkdir -p /tmp/uploadFiles

# Cấu hình upload_tmp_dir cho PHP
# RUN echo "upload_tmp_dir = /tmp/uploadFiles" > /usr/local/etc/php/php.ini

# Cấu hình quyền truy cập cho thư mục
# RUN chown root:root /tmp/uploadFiles
# RUN chmod -R 776 /tmp/uploadFiles
# RUN chmod +t -R /tmp/uploadFiles

# allow rewrite
RUN a2enmod rewrite
# Cho phép override cấu hình Apache từ .htaccess
RUN sed -ri -e 's!/etc/apache2/sites-available!${APACHE_CONFDIR}!g' /etc/apache2/apache2.conf && \
sed -ri -e 's!/etc/apache2/mods-available!${APACHE_CONFDIR}!g' /etc/apache2/apache2.conf

### thay đổi kick thước upload
RUN echo "upload_max_filesize = 60M" >> /usr/local/etc/php/php.ini
RUN echo "post_max_size = 100M" >> /usr/local/etc/php/php.ini

# Kiểm tra cấu hình của Apache
# RUN sed -ri -e 's!^(\s*#?\s*LimitRequestBody)\s+[0-9]+!\1 2097152!g' /etc/apache2/apache2.conf

RUN chown -R www-data:www-data /var/www/html/public/products

RUN find . -type d -exec chmod 750 {} \;

# config folder to uploads file
RUN chmod -R 774 /var/www/html/public/products

RUN find /var/www/html/public/products -type d -exec chmod 774 {} \;

RUN find . -type f -exec chmod 640 {} \;

RUN chmod +t -R /var/www/html/

RUN service apache2 restart
