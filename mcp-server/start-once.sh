#!/usr/bin/env sh
set -eu

SCRIPT_DIR=$(CDPATH= cd -- "$(dirname -- "$0")" && pwd)
cd "$SCRIPT_DIR"

if pgrep -f '[n]ode .*mcp-server/server.js' >/dev/null 2>&1 || pgrep -f '[n]ode server.js' >/dev/null 2>&1; then
    echo "MCP server is already running."
    exit 0
fi

# Local default for development convenience.
: "${MCP_API_KEY:=dev-local-key}"
export MCP_API_KEY

exec node server.js
