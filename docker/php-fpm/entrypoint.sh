#!/bin/bash

# Install backend dependencies
composer install

# Wait until MySQL container is ready
until php bin/console doctrine:query:sql "select 1" >/dev/null 2>&1; do
	(>&2 echo "Waiting for MySQL to be ready...")
	sleep 1
done

# Create and populate database
php bin/console doctrine:migrations:migrate --allow-no-migration --no-interaction
php bin/console doctrine:fixtures:load --no-interaction

# Consume messages created by fixtures
php bin/console messenger:consume async --limit=3

# Generate SSH keypair if it does not exist
if [ ! -f /application/config/openssl/private.key ]
then
  openssl genrsa -out /application/config/openssl/private.key 2048
  openssl rsa -in /application/config/openssl/private.key -pubout -out /application/config/openssl/public.key
fi

/usr/sbin/php-fpm8.1
