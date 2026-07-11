#!/usr/bin/env bash

get_compose_file() {
    case "$1" in
        "dev")
            echo "docker-compose.yml"
            ;;
        "prod")
            echo "docker-compose.prod.yml"
            ;;
        "prod-http")
            echo "docker-compose.prod-http.yml"
            ;;
        *)
            echo "docker-compose.yml"
            ;;
    esac
}

# Check if DOCKER_ENV is already set in .env and use it
if [ -f ".env" ]; then
    env_value=$(grep "^DOCKER_ENV=" .env | cut -d'=' -f2)
    if [ -n "$env_value" ]; then
        docker_compose_file=$(get_compose_file "$env_value")
        echo "$docker_compose_file"
        exit 0
    fi
fi

if [ ! -f ".env" ]; then
    if [ -f ".env.example" ]; then
        cp .env.example .env
        echo "✅  Created .env from .env.example" >&2
    else
        echo "❌  Error: .env and .env.example not found in the current directory!" >&2
        exit 1
    fi
fi

read -p "🌿  Choose environment [dev/ip/prod] (default is dev): " env >&2

# Set default to dev if empty input
if [ -z "$env" ]; then
    env="dev"
fi

case "$env" in
    "dev"|"prod"|"prod-http")
        # Valid environment
        ;;
    *)
        echo "⚠️  Invalid environment '$env'. Using 'dev' as default." >&2
        env="dev"
        ;;
esac

docker_compose_file=$(get_compose_file "$env")

if grep -q "^DOCKER_ENV=" .env; then
    # Replace existing DOCKER_ENV line
    sed -i "s/^DOCKER_ENV=.*/DOCKER_ENV=$env/" .env
    echo "✅  Updated DOCKER_ENV=$env in .env file" >&2
else
    # Add DOCKER_ENV to the end of the file
    echo "DOCKER_ENV=$env" >> .env
    echo "✅  Added DOCKER_ENV=$env to .env file" >&2
fi

echo "🐳  Using: $docker_compose_file" >&2

echo "$docker_compose_file"
