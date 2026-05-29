#!/usr/bin/env sh
set -eu

SCRIPT_DIR=$(CDPATH='' cd -- "$(dirname -- "$0")" && pwd)
TARGET_DIR="${XDG_CONFIG_HOME:-$HOME/.config}/containers/systemd"

mkdir -p "$TARGET_DIR"
cp "$SCRIPT_DIR/linavelt.pod" "$TARGET_DIR/"
cp "$SCRIPT_DIR/linavelt-db.container" "$TARGET_DIR/"
cp "$SCRIPT_DIR/linavelt-app.container" "$TARGET_DIR/"
cp "$SCRIPT_DIR/linavelt-mcp.container" "$TARGET_DIR/"

systemctl --user daemon-reload
systemctl --user enable --now linavelt-pod.service
systemctl --user enable --now linavelt-db.service
systemctl --user enable --now linavelt-app.service
systemctl --user enable --now linavelt-mcp.service

echo "Quadlet units installed and started."
