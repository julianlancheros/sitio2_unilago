# Usar una imagen oficial de PHP con Apache preinstalado
FROM php:8.2-apache

# Instalar las dependencias de sistema y los drivers pdo_pgsql de PostgreSQL
RUN apt-get update && apt-get install -y \
    libpq-dev \
    && docker-php-ext-install pdo pdo_pgsql pgsql

# Copiar el código fuente local de la carpeta src al directorio del servidor en el contenedor
COPY src/ /var/www/html/

# Configurar permisos para la ejecución correcta de Apache
RUN chown -R www-data:www-data /var/www/html/

# Exponer el puerto HTTP del contenedor
EXPOSE 80