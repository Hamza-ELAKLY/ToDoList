#!/bin/bash

echo "Starting Soketi WebSocket Server..."
echo "Server will run on: http://127.0.0.1:6001"
echo "Press Ctrl+C to stop the server"
echo ""

# Start Soketi with the configuration file
soketi start --config=soketi.config.json