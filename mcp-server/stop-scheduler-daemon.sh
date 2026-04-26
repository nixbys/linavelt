#!/usr/bin/env sh
set -eu

SCRIPT_DIR=$(CDPATH= cd -- "$(dirname -- "$0")" && pwd)
PID_FILE="$SCRIPT_DIR/.mcp-scheduler.pid"

cd "$SCRIPT_DIR"

if [ -f "$PID_FILE" ]; then
    pid=$(cat "$PID_FILE")
    if [ -n "$pid" ] && kill -0 "$pid" >/dev/null 2>&1; then
        kill "$pid"
        rm -f "$PID_FILE"
        echo "Stopped MCP scheduler (PID $pid)."
        exit 0
    fi
    rm -f "$PID_FILE"
fi

fallback_pid=$(pgrep -f '[n]ode .*mcp-server/scheduler.js|[n]ode scheduler.js' | head -n 1 || true)
if [ -n "${fallback_pid:-}" ]; then
    kill "$fallback_pid"
    echo "Stopped MCP scheduler (PID $fallback_pid)."
    exit 0
fi

echo "MCP scheduler is not running."
