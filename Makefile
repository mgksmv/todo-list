-include .env

DOCKER_COMPOSE_FILE = docker-compose.yml

CONFIRM = \
	read -p "⚠️  Are you sure? [y/N] " ans; \
	if [ "$$ans" != "y" ] && [ "$$ans" != "Y" ]; then \
		echo "Command aborted."; \
		exit 1; \
	fi

init: create-app-network create-traefik-network
	@if [ ! -f .env ] && [ -f .env.example ]; then cp .env.example .env; echo "✅  Created root .env"; fi
	@if [ ! -f backend/.env ] && [ -f backend/.env.example ]; then cp backend/.env.example backend/.env; echo "✅  Created backend/.env"; fi
	@if [ ! -f frontend/.env ] && [ -f frontend/.env.example ]; then cp frontend/.env.example frontend/.env; echo "✅  Created frontend/.env"; fi
	@file=$$(./choose-env.sh) || exit 1; \
	if [ "$$file" = "docker-compose.yml" ]; then \
		backend_init_target=backend-dev-init; \
		frontend_init_target=frontend-dev-init; \
	elif [ "$$file" = "docker-compose.prod-http.yml" ] || [ "$$file" = "docker-compose.prod.yml" ]; then \
		backend_init_target=backend-prod-init; \
		frontend_init_target=frontend-prod-init; \
	fi; \
	$(CONFIRM); \
	$(MAKE) DOCKER_COMPOSE_FILE="$$file" docker-down-clear backend-clear docker-up-build "$$backend_init_target" "$$frontend_init_target"

up: create-app-network create-traefik-network
	@file=$$(./choose-env.sh) || exit 1; \
	$(MAKE) DOCKER_COMPOSE_FILE="$$file" docker-up

down: create-app-network create-traefik-network
	@file=$$(./choose-env.sh) || exit 1; \
	$(MAKE) DOCKER_COMPOSE_FILE="$$file" docker-down

down-clear: create-app-network create-traefik-network
	@file=$$(./choose-env.sh) || exit 1; \
	$(CONFIRM); \
	$(MAKE) DOCKER_COMPOSE_FILE="$$file" docker-down-clear

restart: create-app-network create-traefik-network
	@file=$$(./choose-env.sh) || exit 1; \
	$(MAKE) DOCKER_COMPOSE_FILE="$$file" docker-down docker-up

build: create-app-network create-traefik-network
	@file=$$(./choose-env.sh) || exit 1; \
	if [ "$$file" = "docker-compose.yml" ]; then \
		build_target=dev-app-build; \
	elif [ "$$file" = "docker-compose.prod-http.yml" ] || [ "$$file" = "docker-compose.prod.yml" ]; then \
		build_target=prod-app-build; \
	fi; \
	$(MAKE) DOCKER_COMPOSE_FILE="$$file" docker-down docker-up-build "$$build_target"

create-app-network:
	@docker network inspect app >/dev/null 2>&1 || \
	(docker network create app && echo "✅  Created app network")

create-traefik-network:
	@docker network inspect traefik_public >/dev/null 2>&1 || \
	(docker network create traefik_public && echo "✅  Created traefik_public network")

update-deps: backend-composer-update frontend-npm-update restart

docker-up:
	docker compose -f $(DOCKER_COMPOSE_FILE) up -d

docker-up-build:
	docker compose -f $(DOCKER_COMPOSE_FILE) up -d --build --remove-orphans

docker-down:
	docker compose -f $(DOCKER_COMPOSE_FILE) down --remove-orphans

docker-down-clear:
	docker compose -f $(DOCKER_COMPOSE_FILE) down -v --remove-orphans

docker-pull:
	docker compose -f $(DOCKER_COMPOSE_FILE) pull

docker-build:
	docker compose -f $(DOCKER_COMPOSE_FILE) build --pull

backend-%:
	$(MAKE) -C backend $*

frontend-%:
	$(MAKE) -C frontend $*
