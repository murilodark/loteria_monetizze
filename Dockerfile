FROM php:8.3-fpm

# Seta o nome do usuario do ambiente
# ARG user=user_loterias
# ARG uid=1000

# Instala as dependências necessárias
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip \
    default-mysql-client

# Instala as extensões do PHP
RUN docker-php-ext-install pdo_mysql mysqli mbstring exif pcntl bcmath gd sockets

# Instala o Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Define o diretório de trabalho do projeto
WORKDIR /var/www

# Copia o arquivo de configuração customizada do PHP
COPY docker/php/custom.ini /usr/local/etc/php/conf.d/custom.ini

# Copia os arquivos do projeto para o contêiner
# COPY . /var/www

# Define a variável de ambiente para permitir o Composer como root
# ENV COMPOSER_ALLOW_SUPERUSER=1

# Instala as dependências do Composer
# RUN composer install

# USER $user
