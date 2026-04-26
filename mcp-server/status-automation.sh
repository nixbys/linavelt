#!/usr/bin/env sh
set -eu

SCRIPT_DIR=$(CDPATH= cd -- "$(dirname -- "$0")" && pwd)

cd "$SCRIPT_DIR"
printf "server: "
sh ./status-daemon.sh
printf "scheduler: "
sh ./status-scheduler-daemon.sh
