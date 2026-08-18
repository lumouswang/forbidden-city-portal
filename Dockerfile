FROM php:8.2-cli

WORKDIR /app

# Copy all files
COPY . /app/

# Expose port
EXPOSE 8080

# Start PHP built-in server
CMD ["sh", "-c", "php -S 0.0.0.0:${PORT:-8080} -t . server.php"]