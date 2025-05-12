# Imagen base con PHP y NGINX
FROM php:8.2-fpm

# Instala dependencias y extensiones
RUN apt-get update && apt-get install -y nginx && docker-php-ext-install pdo pdo_mysql

# Copia archivos de tu proyecto
COPY . /var/www/html
COPY nginx.conf /etc/nginx/sites-available/default

# Configura permisos
RUN chown -R www-data:www-data /var/www/html

# Expone el puerto que usará NGINX
EXPOSE 8080

# Inicia NGINX y PHP-FPM
CMD service nginx start && php-fpm

