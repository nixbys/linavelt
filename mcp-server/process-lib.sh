#!/usr/bin/env sh
set -eu

start_process() {
    pid_file="$1"
    process_pattern="$2"
    log_file="$3"
    process_name="$4"
    shift 4

    if [ -f "$pid_file" ]; then
        existing_pid=$(cat "$pid_file")
        if [ -n "$existing_pid" ] && kill -0 "$existing_pid" >/dev/null 2>&1; then
            echo "$process_name is already running (PID $existing_pid)."
            return 0
        fi
        rm -f "$pid_file"
    fi

    existing_process_pid=$(pgrep -f "$process_pattern" | head -n 1 || true)
    if [ -n "${existing_process_pid:-}" ]; then
        echo "$existing_process_pid" > "$pid_file"
        echo "$process_name already running (PID $existing_process_pid). PID file synchronized."
        return 0
    fi

    nohup "$@" >> "$log_file" 2>&1 &
    new_pid=$!
    echo "$new_pid" > "$pid_file"

    echo "$process_name started in background (PID $new_pid)."
}

stop_process() {
    pid_file="$1"
    process_pattern="$2"
    process_name="$3"

    if [ -f "$pid_file" ]; then
        pid=$(cat "$pid_file")
        if [ -n "$pid" ] && kill -0 "$pid" >/dev/null 2>&1; then
            kill "$pid"
            rm -f "$pid_file"
            echo "Stopped $process_name (PID $pid)."
            return 0
        fi
        rm -f "$pid_file"
    fi

    fallback_pid=$(pgrep -f "$process_pattern" | head -n 1 || true)
    if [ -n "${fallback_pid:-}" ]; then
        kill "$fallback_pid"
        echo "Stopped $process_name (PID $fallback_pid)."
        return 0
    fi

    echo "$process_name is not running."
}

status_process() {
    pid_file="$1"
    process_pattern="$2"

    if [ -f "$pid_file" ]; then
        pid=$(cat "$pid_file")
        if [ -n "$pid" ] && kill -0 "$pid" >/dev/null 2>&1; then
            echo "running (PID $pid)"
            return 0
        fi
        rm -f "$pid_file"
    fi

    fallback_pid=$(pgrep -f "$process_pattern" | head -n 1 || true)
    if [ -n "${fallback_pid:-}" ]; then
        echo "running (PID $fallback_pid, no PID file)"
        return 0
    fi

    echo "stopped"
}