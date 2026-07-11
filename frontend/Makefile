include .env

CONFIRM = \
	read -p "⚠️  Are you sure? [y/N] " ans; \
	if [ "$$ans" != "y" ] && [ "$$ans" != "Y" ]; then \
		echo "Command aborted."; \
		exit 1; \
	fi

dev-init: npm-install
dev-build: npm-install
prod-init: npm-install npm-build
prod-build: npm-install npm-build

npm-install:
	docker compose run --rm --no-deps nuxt-app npm install

npm-update:
	docker compose run --rm --no-deps nuxt-app npm update

npm-build:
	docker compose run --rm --no-deps nuxt-app npm run build
