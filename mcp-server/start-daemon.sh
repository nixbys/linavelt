#!/usr/bin/env sh
set -eu

SCRIPT_DIR=$(CDPATH='' cd -- "$(dirname -- "$0")" && pwd)
PID_FILE="$SCRIPT_DIR/.mcp-server.pid"
PROCESS_PATTERN='[n]ode .*mcp-server/server.js|[n]ode server.js'

cd "$SCRIPT_DIR"
. "$SCRIPT_DIR/process-lib.sh"

# Local default for development convenience.
: "${MCP_API_KEY:=dev-local-key}"
export MCP_API_KEY

start_process "$PID_FILE" "$PROCESS_PATTERN" "$SCRIPT_DIR/server.log" "MCP server" node server.js
echo "Health: http://127.0.0.1:4000/health"
