FROM php:8.2-cli

WORKDIR /app

# Copy all files
COPY . /app/

# Expose port (documentation; Railway sets PORT env var)
EXPOSE 8080

# Run PHP built-in server in foreground, logs go to stdout (visible in Railway Deploy Logs)
CMD ["sh", "-c", "php -S 0.0.0.0:${PORT:-8080} -t ."]