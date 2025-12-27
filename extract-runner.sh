#!/bin/bash

# Check if the actions-runner directory exists
if [ ! -d "actions-runner" ]; then
    echo "Extracting actions runner files..."
    tar -xzvf actions-runner.tar.gz
fi

# Configure the runner
./actions-runner/config.sh --url https://github.com/nixbys/linavelt --token YOUR_TOKEN

# Start the runner
./actions-runner/run.sh
