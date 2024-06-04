FROM php:8.2-fpm

# Instalando dependências necessárias para o PHP e outras ferramentas
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libmariadb-dev \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip \
    libzip-dev

# Limpar cache e diretórios temporários para reduzir o tamanho da imagem
RUN apt-get clean && rm -rf /var/lib/apt/lists/*

# Instalar extensões PHP necessárias
RUN docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd sockets

# Instalar a extensão zip com suporte ao libzip
RUN pecl install zip && docker-php-ext-enable zip

# Instalar o Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Criar o diretório de trabalho e configurar
RUN mkdir -p /var/www
WORKDIR /var/www

COPY ./docker/scripts/entry.sh /usr/local/bin/entry.sh

RUN chmod +x /usr/local/bin/entry.sh

ENTRYPOINT ["/usr/local/bin/entry.sh"]

CMD ["php-fpm"]
