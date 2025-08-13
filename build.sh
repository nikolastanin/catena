#!/bin/bash

# Build script for Slots Plugin with Vite

echo "Building Slots Plugin..."

# Check if node_modules exists
if [ ! -d "node_modules" ]; then
    echo "Installing dependencies..."
    npm install
fi

# Build the project
echo "Building with Vite..."
npm run build

if [ $? -eq 0 ]; then
    echo "✅ Build successful!"
    echo "Built assets are in assets/dist/"
else
    echo "❌ Build failed!"
    exit 1
fi
