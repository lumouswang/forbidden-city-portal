FROM php:8.2-cli-alpine

WORKDIR /app

# Copy all files
COPY . /app/

# Expose port
EXPOSE 8080

# Use exec form, run PHP built-in server in foreground
# The server stays alive handling requests forever
CMD ["sh", "-c", "exec php -S 0.0.0.0:${PORT:-8080} -t ."]