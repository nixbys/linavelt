#!/usr/bin/env sh
set -eu

SCRIPT_DIR=$(CDPATH= cd -- "$(dirname -- "$0")" && pwd)
PID_FILE="$SCRIPT_DIR/.mcp-server.pid"

cd "$SCRIPT_DIR"

if [ -f "$PID_FILE" ]; then
    existing_pid=$(cat "$PID_FILE")
    if [ -n "$existing_pid" ] && kill -0 "$existing_pid" >/dev/null 2>&1; then
        echo "MCP server is already running (PID $existing_pid)."
        exit 0
    fi
    rm -f "$PID_FILE"
fi

existing_process_pid=$(pgrep -f '[n]ode .*mcp-server/server.js|[n]ode server.js' | head -n 1 || true)
if [ -n "${existing_process_pid:-}" ]; then
    echo "$existing_process_pid" > "$PID_FILE"
    echo "MCP server already running (PID $existing_process_pid). PID file synchronized."
    exit 0
fi

# Local default for development convenience.
: "${MCP_API_KEY:=dev-local-key}"
export MCP_API_KEY

nohup node server.js >> "$SCRIPT_DIR/server.log" 2>&1 &
new_pid=$!
echo "$new_pid" > "$PID_FILE"

echo "MCP server started in background (PID $new_pid)."
echo "Health: http://127.0.0.1:4000/health"
