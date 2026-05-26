#!/usr/bin/env sh
set -eu

ROOT_DIR=$(CDPATH='' cd -- "$(dirname -- "$0")/.." && pwd)
MCP_DIR="$ROOT_DIR/mcp-server"

fail_count=0
warn_count=0

print_section() {
    printf "\n==> %s\n" "$1"
}

run_required() {
    step="$1"
    shift

    printf "[RUN] %s\n" "$step"
    if "$@"; then
        printf "[PASS] %s\n" "$step"
    else
        printf "[FAIL] %s\n" "$step"
        fail_count=$((fail_count + 1))
    fi
}

run_optional() {
    step="$1"
    shift

    printf "[RUN] %s\n" "$step"
    if "$@"; then
        printf "[PASS] %s\n" "$step"
    else
        printf "[WARN] %s\n" "$step"
        warn_count=$((warn_count + 1))
    fi
}

command_exists() {
    command -v "$1" >/dev/null 2>&1
}

print_section "Environment"
run_required "Node is available" command_exists node
run_required "npm is available" command_exists npm

if command_exists composer; then
    run_required "Composer manifest validates" sh -c "cd '$ROOT_DIR' && composer validate --no-check-all --strict"
else
    printf "[WARN] Composer is not installed; PHP checks skipped.\n"
    warn_count=$((warn_count + 1))
fi

if command_exists php; then
    if [ -f "$ROOT_DIR/artisan" ]; then
        run_required "Laravel tests pass" sh -c "cd '$ROOT_DIR' && php artisan test"
    fi

    if [ -x "$ROOT_DIR/vendor/bin/pint" ]; then
        run_required "Pint formatting check" sh -c "cd '$ROOT_DIR' && ./vendor/bin/pint --test"
    else
        printf "[WARN] vendor/bin/pint not found or not executable; formatting check skipped.\n"
        warn_count=$((warn_count + 1))
    fi
else
    printf "[WARN] PHP is not installed; Laravel test and Pint checks skipped.\n"
    warn_count=$((warn_count + 1))
fi

print_section "Frontend"
run_required "Vite production build" sh -c "cd '$ROOT_DIR' && npm run build"
run_required "Root npm audit (prod)" sh -c "cd '$ROOT_DIR' && npm audit --omit=dev"

print_section "MCP Server"
run_required "MCP npm audit (prod)" sh -c "cd '$MCP_DIR' && npm audit --omit=dev"
run_optional "MCP automation readiness" sh -c "cd '$MCP_DIR' && npm run automation:readiness"

print_section "Summary"
printf "Required failures: %s\n" "$fail_count"
printf "Warnings: %s\n" "$warn_count"

if [ "$fail_count" -gt 0 ]; then
    exit 1
fi

exit 0
