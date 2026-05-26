#!/usr/bin/env sh
set -eu

SCRIPT_DIR=$(CDPATH='' cd -- "$(dirname -- "$0")" && pwd)
PID_FILE="$SCRIPT_DIR/.mcp-server.pid"
PROCESS_PATTERN='[n]ode .*mcp-server/server.js|[n]ode server.js'

cd "$SCRIPT_DIR"
. "$SCRIPT_DIR/process-lib.sh"

status_process "$PID_FILE" "$PROCESS_PATTERN"
