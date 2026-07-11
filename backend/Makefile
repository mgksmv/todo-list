include .env

CONFIRM = \
	read -p "⚠️  Are you sure? [y/N] " ans; \
	if [ "$$ans" != "y" ] && [ "$$ans" != "Y" ]; then \
		echo "Command aborted."; \
		exit 1; \
	fi

dev-init: composer-install wait-db migrate generate-key storage-link set-permissions db-seed
dev-build: composer-install wait-db migrate storage-link set-permissions
prod-init: composer-install wait-db migrate generate-key storage-link set-permissions optimize
prod-build: composer-install wait-db migrate storage-link set-permissions optimize

RUN_COMMAND = docker compose run --rm --no-deps php-fpm

clear:
	docker run --rm -v ${PWD}:/app -w /app alpine sh -c \
		'rm -rf storage/framework/cache/data/* storage/framework/sessions/* storage/framework/testing/* storage/framework/views/* storage/logs/*'

set-permissions:
	$(RUN_COMMAND) chmod 777 -R storage bootstrap/cache

composer-install:
	$(RUN_COMMAND) composer install

composer-update:
	$(RUN_COMMAND) composer update

composer-dump-autoload:
	$(RUN_COMMAND) composer dump-autoload

wait-db:
	docker compose run --rm php-fpm wait-for-it db:${DB_PORT} -t 30

migrate:
	$(RUN_COMMAND) php artisan migrate --force

generate-key:
	$(RUN_COMMAND) php artisan key:generate

storage-link:
	$(RUN_COMMAND) php artisan storage:link

optimize:
	$(RUN_COMMAND) php artisan config:cache
	$(RUN_COMMAND) php artisan route:cache

db-seed:
	$(RUN_COMMAND) php artisan db:seed
