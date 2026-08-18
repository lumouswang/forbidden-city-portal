FROM php:8.2-cli-alpine

WORKDIR /app

# Copy all files
COPY . /app/

# Expose port
EXPOSE 8080

# Start PHP built-in server WITHOUT router (default behavior works perfectly)
CMD ["sh", "-c", "exec php -S 0.0.0.0:${PORT:-8080} -t ."]