#!/usr/bin/env sh
set -eu

SCRIPT_DIR=$(CDPATH= cd -- "$(dirname -- "$0")" && pwd)
PID_FILE="$SCRIPT_DIR/.mcp-scheduler.pid"

cd "$SCRIPT_DIR"

if [ -f "$PID_FILE" ]; then
    pid=$(cat "$PID_FILE")
    if [ -n "$pid" ] && kill -0 "$pid" >/dev/null 2>&1; then
        echo "running (PID $pid)"
        exit 0
    fi
    rm -f "$PID_FILE"
fi

fallback_pid=$(pgrep -f '[n]ode .*mcp-server/scheduler.js|[n]ode scheduler.js' | head -n 1 || true)
if [ -n "${fallback_pid:-}" ]; then
    echo "running (PID $fallback_pid, no PID file)"
    exit 0
fi

echo "stopped"
