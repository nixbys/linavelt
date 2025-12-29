# Base image for Laravel
FROM fedora:latest

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
    nodejs \
    npm \
    composer \
    git \
    unzip \
    && dnf clean all

# Set working directory
WORKDIR /var/www/html

# Copy Laravel app
COPY . .

# Add a non-root user for running the application
RUN useradd -m -s /bin/bash appuser

# Switch to the non-root user
USER appuser

# Remove sensitive files from the image
RUN rm -rf /var/www/html/.env /var/www/html/storage/oauth-private.key /var/www/html/storage/oauth-public.key

# Ensure proper permissions for the copied files
RUN chmod -R 755 /var/www/html && chown -R appuser:appuser /var/www/html

# Enable multi-stage builds to reduce image size
FROM scratch AS final
COPY --from=0 /var/www/html /var/www/html

# Final CMD remains unchanged
CMD ["npm", "run", "electron:start"]
