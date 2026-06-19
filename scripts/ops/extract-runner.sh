#!/bin/bash
set -euo pipefail

# Check if the actions-runner directory exists
if [ ! -d "actions-runner" ]; then
    echo "Extracting actions runner files..."
    tar -xzvf actions-runner.tar.gz
fi

# Require the registration token via the RUNNER_TOKEN environment variable only.
# NOTE: The GitHub Actions runner config.sh requires --token on the command line;
# there is no stdin or file-based alternative supported by the upstream tool.
# To minimise exposure, always set RUNNER_TOKEN as a shell environment variable
# in a restricted session rather than passing it as a positional argument.
if [ -z "${RUNNER_TOKEN:-}" ]; then
    echo "Error: RUNNER_TOKEN environment variable must be set." >&2
    exit 1
fi

# Configure the runner
./actions-runner/config.sh --url https://github.com/nixbys/linavelt --token "$RUNNER_TOKEN"

# Start the runner
./actions-runner/run.sh
