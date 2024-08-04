FROM php:8.3-fpm

# seta o nome do usuario do ambiente
# ARG user=user_loterias
# ARG uid=1000



# instala as dependencias necessarias
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip \
    default-mysql-client


# instala as extensões
RUN docker-php-ext-install pdo_mysql mysqli mbstring exif pcntl bcmath gd sockets

# seto o diretório do projeto
WORKDIR /var/www



# defino o caminho para a configurações e customizações do php
COPY docker/php/custom.ini /usr/local/etc/php/conf.d/custom.ini

# USER $user
