#!/usr/bin/env sh
set -eu

SCRIPT_DIR=$(CDPATH='' cd -- "$(dirname -- "$0")" && pwd)
REPO_ROOT=$(CDPATH='' cd -- "$SCRIPT_DIR/../.." && pwd)

podman build -t localhost/linavelt-app:latest -f "$REPO_ROOT/Containerfile" "$REPO_ROOT"
podman build -t localhost/linavelt-mcp:latest -f "$REPO_ROOT/mcp-server/Containerfile" "$REPO_ROOT/mcp-server"

echo "Built images: localhost/linavelt-app:latest and localhost/linavelt-mcp:latest"
