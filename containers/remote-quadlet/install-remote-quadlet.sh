#!/usr/bin/env sh
set -eu

SCRIPT_DIR=$(CDPATH='' cd -- "$(dirname -- "$0")" && pwd)
TARGET_DIR="${XDG_CONFIG_HOME:-$HOME/.config}/containers/systemd"

GHCR_OWNER=${GHCR_OWNER:-}
IMAGE_TAG=${IMAGE_TAG:-latest}
APP_KEY=${APP_KEY:-}
MCP_API_KEY=${MCP_API_KEY:-}
APP_URL=${APP_URL:-http://127.0.0.1:8000}
MYSQL_ROOT_PASSWORD=${MYSQL_ROOT_PASSWORD:-root}
MYSQL_PASSWORD=${MYSQL_PASSWORD:-password}

escape_sed_replacement() {
    printf '%s' "$1" | sed 's/[\\&|]/\\&/g'
}

if [ -z "$GHCR_OWNER" ]; then
    echo "GHCR_OWNER is required (for example: nixbys)" >&2
    exit 1
fi

if [ -z "$APP_KEY" ]; then
    echo "APP_KEY is required for the production app container" >&2
    exit 1
fi

if [ -z "$MCP_API_KEY" ]; then
    echo "MCP_API_KEY is required for the production MCP container" >&2
    exit 1
fi

mkdir -p "$TARGET_DIR"

sed \
    -e "s|__GHCR_OWNER__|$(escape_sed_replacement "$GHCR_OWNER")|g" \
    -e "s|__IMAGE_TAG__|$(escape_sed_replacement "$IMAGE_TAG")|g" \
    -e "s|__APP_KEY__|$(escape_sed_replacement "$APP_KEY")|g" \
    -e "s|__MCP_API_KEY__|$(escape_sed_replacement "$MCP_API_KEY")|g" \
    -e "s|__APP_URL__|$(escape_sed_replacement "$APP_URL")|g" \
    -e "s|__MYSQL_ROOT_PASSWORD__|$(escape_sed_replacement "$MYSQL_ROOT_PASSWORD")|g" \
    -e "s|__MYSQL_PASSWORD__|$(escape_sed_replacement "$MYSQL_PASSWORD")|g" \
    "$SCRIPT_DIR/linavelt.pod" > "$TARGET_DIR/linavelt.pod"

sed \
    -e "s|__GHCR_OWNER__|$(escape_sed_replacement "$GHCR_OWNER")|g" \
    -e "s|__IMAGE_TAG__|$(escape_sed_replacement "$IMAGE_TAG")|g" \
    -e "s|__APP_KEY__|$(escape_sed_replacement "$APP_KEY")|g" \
    -e "s|__MCP_API_KEY__|$(escape_sed_replacement "$MCP_API_KEY")|g" \
    -e "s|__APP_URL__|$(escape_sed_replacement "$APP_URL")|g" \
    -e "s|__MYSQL_ROOT_PASSWORD__|$(escape_sed_replacement "$MYSQL_ROOT_PASSWORD")|g" \
    -e "s|__MYSQL_PASSWORD__|$(escape_sed_replacement "$MYSQL_PASSWORD")|g" \
    "$SCRIPT_DIR/linavelt-db.container" > "$TARGET_DIR/linavelt-db.container"

sed \
    -e "s|__GHCR_OWNER__|$(escape_sed_replacement "$GHCR_OWNER")|g" \
    -e "s|__IMAGE_TAG__|$(escape_sed_replacement "$IMAGE_TAG")|g" \
    -e "s|__APP_KEY__|$(escape_sed_replacement "$APP_KEY")|g" \
    -e "s|__MCP_API_KEY__|$(escape_sed_replacement "$MCP_API_KEY")|g" \
    -e "s|__APP_URL__|$(escape_sed_replacement "$APP_URL")|g" \
    -e "s|__MYSQL_ROOT_PASSWORD__|$(escape_sed_replacement "$MYSQL_ROOT_PASSWORD")|g" \
    -e "s|__MYSQL_PASSWORD__|$(escape_sed_replacement "$MYSQL_PASSWORD")|g" \
    "$SCRIPT_DIR/linavelt-app.container" > "$TARGET_DIR/linavelt-app.container"

sed \
    -e "s|__GHCR_OWNER__|$(escape_sed_replacement "$GHCR_OWNER")|g" \
    -e "s|__IMAGE_TAG__|$(escape_sed_replacement "$IMAGE_TAG")|g" \
    -e "s|__APP_KEY__|$(escape_sed_replacement "$APP_KEY")|g" \
    -e "s|__MCP_API_KEY__|$(escape_sed_replacement "$MCP_API_KEY")|g" \
    -e "s|__APP_URL__|$(escape_sed_replacement "$APP_URL")|g" \
    -e "s|__MYSQL_ROOT_PASSWORD__|$(escape_sed_replacement "$MYSQL_ROOT_PASSWORD")|g" \
    -e "s|__MYSQL_PASSWORD__|$(escape_sed_replacement "$MYSQL_PASSWORD")|g" \
    "$SCRIPT_DIR/linavelt-mcp.container" > "$TARGET_DIR/linavelt-mcp.container"

systemctl --user daemon-reload
systemctl --user enable --now linavelt-pod.service
systemctl --user enable --now linavelt-db.service
systemctl --user enable --now linavelt-app.service
systemctl --user enable --now linavelt-mcp.service

echo "Remote quadlet units installed and started from GHCR images."
