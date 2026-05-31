FROM php:8.2-cli-bookworm

RUN docker-php-ext-install mysqli pdo_mysql

WORKDIR /app

COPY . .

EXPOSE 8080

# Railway sets PORT at runtime — must bind 0.0.0.0
CMD ["sh", "-c", "exec php -S 0.0.0.0:${PORT:-8080} -t ."]
