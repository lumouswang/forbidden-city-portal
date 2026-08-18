FROM php:8.2-cli

WORKDIR /app

# Copy all files
COPY . /app/

# Railway will set PORT env var, default to 8080 if not set
# EXPOSE is documentation; Railway's network layer detects via PORT
EXPOSE 8080

# Use exec form so PHP gets the correct PORT signal
CMD ["sh", "-c", "echo \"PORT=$PORT\" && php -S 0.0.0.0:${PORT:-8080} -t . > /tmp/php-server.log 2>&1"]