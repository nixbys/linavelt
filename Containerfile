# ── Stage 1: builder ─────────────────────────────────────────────────────────
FROM fedora:latest AS builder

# Install system dependencies
RUN dnf -y update && dnf -y install \
    php \
    php-cli \
    php-mysqlnd \
    php-pdo \
    php-mbstring \
    php-xml \
    php-bcmath \
    php-json \
    php-curl \
    php-zip \
    nodejs \
    npm \
    git \
    unzip \
    && dnf clean all

# Install Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

# Copy dependency manifests first for layer caching
COPY composer.json composer.lock ./
RUN composer install --no-dev --no-scripts --no-autoloader --prefer-dist

COPY package.json package-lock.json* ./
RUN npm ci --ignore-scripts

# Copy the rest of the application
COPY . .

# Remove secrets before they reach the runtime stage
RUN rm -f .env storage/oauth-private.key storage/oauth-public.key

# Finish composer autoloader and npm build
RUN composer dump-autoload --optimize
RUN npm run build

# Fix permissions while still root
RUN useradd -r -s /bin/false -u 1001 appuser \
    && chown -R appuser:appuser /var/www/html \
    && chmod -R 755 /var/www/html \
    && chmod -R 775 storage bootstrap/cache

# ── Stage 2: runtime ─────────────────────────────────────────────────────────
FROM fedora:latest AS final

RUN dnf -y update && dnf -y install \
    php \
    php-cli \
    php-mysqlnd \
    php-pdo \
    php-mbstring \
    php-xml \
    php-bcmath \
    php-json \
    php-curl \
    php-zip \
    && dnf clean all

RUN useradd -r -s /bin/false -u 1001 appuser

WORKDIR /var/www/html

COPY --from=builder --chown=appuser:appuser /var/www/html .

EXPOSE 8000

USER appuser

CMD ["php", "artisan", "serve", "--host=0.0.0.0", "--port=8000"]
