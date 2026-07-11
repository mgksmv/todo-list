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

db-dump:
	@read -p "Путь для сохранения дампа (нажмите Enter для сохранения в текущей директории): " dump_path; \
	if [ -z "$$dump_path" ]; then \
		dump_path="./dump_$$(date +%Y%m%d_%H%M%S).sql"; \
	fi; \
	db_password=$$(grep DB_ROOT_PASSWORD .env | cut -d '=' -f2); \
	db_name=$$(grep DB_DATABASE .env | cut -d '=' -f2); \
	echo "Создание дампа базы данных..."; \
	docker compose exec -T db mysqldump --ssl=OFF -u root --password=$$db_password $$db_name > "$$dump_path"; \
	echo "Дамп сохранён в: $$dump_path"

db-import:
	@read -p "Путь к файлу дампа: " dump_file; \
	if [ -z "$$dump_file" ]; then \
		echo "Ошибка: не указан путь к файлу"; \
		exit 1; \
	fi; \
	if [ ! -f "$$dump_file" ]; then \
		echo "Ошибка: файл не найден: $$dump_file"; \
		exit 1; \
	fi; \
	db_password=$$(grep DB_ROOT_PASSWORD .env | cut -d '=' -f2); \
	db_name=$$(grep DB_DATABASE .env | cut -d '=' -f2); \
	$(CONFIRM); \
	echo "Импорт дампа в базу данных..."; \
	docker compose exec -T db mysql --ssl=OFF -u root --password=$$db_password $$db_name < "$$dump_file"; \
	echo "Импорт завершён"
