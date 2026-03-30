#!/bin/bash

# Check if the actions-runner directory exists
if [ ! -d "actions-runner" ]; then
    echo "Extracting actions runner files..."
    tar -xzvf actions-runner.tar.gz
fi

# Require the registration token to be passed as an argument or env var to avoid
# committing secrets into version control.
RUNNER_TOKEN="${RUNNER_TOKEN:-${1:-}}"
if [ -z "$RUNNER_TOKEN" ]; then
    echo "Error: RUNNER_TOKEN environment variable (or first argument) must be set." >&2
    exit 1
fi

# Configure the runner
./actions-runner/config.sh --url https://github.com/nixbys/linavelt --token "$RUNNER_TOKEN"

# Start the runner
./actions-runner/run.sh
