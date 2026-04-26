#!/usr/bin/env sh
set -eu

SCRIPT_DIR=$(CDPATH= cd -- "$(dirname -- "$0")" && pwd)
PID_FILE="$SCRIPT_DIR/.mcp-scheduler.pid"

cd "$SCRIPT_DIR"

if [ -f "$PID_FILE" ]; then
    existing_pid=$(cat "$PID_FILE")
    if [ -n "$existing_pid" ] && kill -0 "$existing_pid" >/dev/null 2>&1; then
        echo "MCP scheduler is already running (PID $existing_pid)."
        exit 0
    fi
    rm -f "$PID_FILE"
fi

existing_process_pid=$(pgrep -f '[n]ode .*mcp-server/scheduler.js|[n]ode scheduler.js' | head -n 1 || true)
if [ -n "${existing_process_pid:-}" ]; then
    echo "$existing_process_pid" > "$PID_FILE"
    echo "MCP scheduler already running (PID $existing_process_pid). PID file synchronized."
    exit 0
fi

nohup node scheduler.js >> "$SCRIPT_DIR/scheduler.log" 2>&1 &
new_pid=$!
echo "$new_pid" > "$PID_FILE"

echo "MCP scheduler started in background (PID $new_pid)."
