FROM php:8.2-cli-alpine

WORKDIR /app

# Copy all files
COPY . /app/

# Expose port
EXPOSE 8080

# Use wrapper script that auto-restarts PHP server if it dies
CMD ["sh", "/app/start.sh"]