FROM php:8.2-fpm-alpine

# Install nginx
RUN apk add --no-cache nginx

WORKDIR /app

# Copy all files
COPY . /app/

# Copy nginx config template
COPY nginx.conf /etc/nginx/nginx.conf

# Make sure runtime dirs exist
RUN mkdir -p /tmp /var/log/nginx && \
    chown -R nginx:nginx /var/log/nginx 2>/dev/null || true

# Expose port (Railway will set PORT env var)
EXPOSE 8080

# Entrypoint script that uses Railway's PORT env var
COPY entrypoint.sh /entrypoint.sh
RUN chmod +x /entrypoint.sh

ENTRYPOINT ["/entrypoint.sh"]