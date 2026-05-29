#!/usr/bin/env sh
set -eu

SCRIPT_DIR=$(CDPATH='' cd -- "$(dirname -- "$0")" && pwd)
REPO_ROOT=$(CDPATH='' cd -- "$SCRIPT_DIR/../.." && pwd)

if ! command -v podman >/dev/null 2>&1; then
    echo "podman is required but not found in PATH" >&2
    exit 1
fi

APP_KEY_VALUE=${APP_KEY:-}
if [ -z "$APP_KEY_VALUE" ]; then
    APP_KEY_VALUE="$(openssl rand -base64 32 | tr -d '\n' | sed 's#^#base64:#')"
fi

MCP_API_KEY_VALUE=${MCP_API_KEY:-dev-local-key}

cd "$REPO_ROOT"

# Build images from project Containerfiles.
sh ./containers/quadlet/build-images.sh

# Stop local MCP daemon if it is using port 4000.
if [ -f "$REPO_ROOT/mcp-server/stop-daemon.sh" ]; then
    sh "$REPO_ROOT/mcp-server/stop-daemon.sh" || true
fi

podman pod rm -f linavelt >/dev/null 2>&1 || true
podman rm -f linavelt-db linavelt-app linavelt-mcp >/dev/null 2>&1 || true
podman volume create linavelt-db-data >/dev/null || true

podman pod create --name linavelt \
    -p 8000:8000 \
    -p 4000:4000 \
    -p 3306:3306 >/dev/null

podman run -d --name linavelt-db --pod linavelt \
    -e MYSQL_ROOT_PASSWORD=root \
    -e MYSQL_DATABASE=laravel \
    -e MYSQL_USER=laravel \
    -e MYSQL_PASSWORD=password \
    -v linavelt-db-data:/var/lib/mysql:Z \
    docker.io/library/mariadb:10.11 >/dev/null

podman run -d --name linavelt-app --pod linavelt \
    -e APP_ENV=production \
    -e APP_DEBUG=false \
    -e APP_URL=http://127.0.0.1:8000 \
    -e APP_KEY="$APP_KEY_VALUE" \
    -e DB_CONNECTION=mysql \
    -e DB_HOST=127.0.0.1 \
    -e DB_PORT=3306 \
    -e DB_DATABASE=laravel \
    -e DB_USERNAME=laravel \
    -e DB_PASSWORD=password \
    -e QUEUE_CONNECTION=database \
    localhost/linavelt-app:latest >/dev/null

podman run -d --name linavelt-mcp --pod linavelt \
    -e MCP_HOST_BIND=0.0.0.0 \
    -e MCP_PORT=4000 \
    -e MCP_API_KEY="$MCP_API_KEY_VALUE" \
    localhost/linavelt-mcp:latest >/dev/null

# Initialize DB schema used by Laravel sessions and app tables.
podman exec linavelt-app php artisan migrate --force

printf "\nPod status:\n"
podman pod ps --format "{{.Name}}|{{.Status}}"
printf "\nContainers:\n"
podman ps --format "{{.Names}}|{{.Status}}" | grep '^linavelt-'
printf "\nEndpoints:\n"
curl -sS http://127.0.0.1:4000/health
printf "\n"
curl -sS -o /dev/null -w "Laravel HTTP %{http_code}\n" http://127.0.0.1:8000

echo
printf "APP_KEY used: %s\n" "$APP_KEY_VALUE"
printf "MCP_API_KEY used: %s\n" "$MCP_API_KEY_VALUE"
