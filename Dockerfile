```dockerfile
FROM php:8.2-cli

# Dependências do sistema e extensões PHP
RUN apt-get update && apt-get install -y \
    git curl zip unzip \
    libzip-dev libonig-dev libxml2-dev \
    python3 python3-pip \
    && docker-php-ext-install \
    pdo pdo_mysql mbstring zip bcmath \
    && (pip install --break-system-packages pymupdf || pip install pymupdf || true) \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

# Composer
COPY --from=composer:2 /usr/bin/composer /usr/local/bin/composer

# Node.js e npm
COPY --from=node:22 /usr/local/bin/node /usr/local/bin/node
COPY --from=node:22 /usr/local/bin/npm /usr/local/bin/npm
COPY --from=node:22 /usr/local/lib/node_modules /usr/local/lib/node_modules

RUN ln -sf /usr/local/lib/node_modules/npm/bin/npm-cli.js /usr/local/bin/npm

WORKDIR /var/www

# Dependências PHP
COPY composer.json composer.lock ./
RUN composer install --no-dev --optimize-autoloader --no-interaction

# Dependências Node
COPY package.json package-lock.json ./
RUN npm ci

# Código do projeto
COPY . .

# Compilação dos arquivos CSS e JS
RUN npm run build

# Permissões
RUN chown -R www-data:www-data storage bootstrap/cache \
    && chmod -R 775 storage bootstrap/cache

EXPOSE 8000

CMD ["php", "artisan", "serve", "--host=0.0.0.0", "--port=8000"]
```