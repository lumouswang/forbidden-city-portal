FROM php:8.2-fpm-alpine

# Install nginx
RUN apk add --no-cache nginx

WORKDIR /app

# Copy all files
COPY . /app/

# Copy nginx config
COPY nginx.conf /etc/nginx/nginx.conf

# Expose port
EXPOSE 8080

# Start php-fpm in background, then start nginx in foreground
CMD ["sh", "-c", "php-fpm -D && nginx -g 'daemon off;'"]