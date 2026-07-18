#!/bin/bash
# EAY Sports smoke driver — launches the app and verifies key routes respond.
# Usage: bash .claude/skills/run-eay-sports/smoke.sh
# Exits 0 if all checks pass, 1 otherwise.

set -e

PORT=8000
BASE_URL="http://127.0.0.1:$PORT"
SERVER_PID=""
FAILED=0

cleanup() {
    if [ -n "$SERVER_PID" ]; then
        kill "$SERVER_PID" 2>/dev/null || true
        wait "$SERVER_PID" 2>/dev/null || true
    fi
}
trap cleanup EXIT

echo "=== EAY Sports Smoke Test ==="

# Build frontend if manifest missing
if [ ! -f "public/build/manifest.json" ]; then
    echo "[build] Vite manifest missing, running npm run build..."
    npm run build
fi

# Start Laravel server in background
echo "[start] Launching php artisan serve on port $PORT..."
php artisan serve --port=$PORT --no-interaction > /dev/null 2>&1 &
SERVER_PID=$!

# Wait for server to be ready (max 10 seconds)
echo "[wait] Waiting for server..."
for i in $(seq 1 20); do
    if curl -s -o /dev/null -w "%{http_code}" "$BASE_URL" 2>/dev/null | grep -qE "^(200|302|303)"; then
        echo "[ready] Server is up."
        break
    fi
    if [ $i -eq 20 ]; then
        echo "[FAIL] Server did not start within 10 seconds."
        exit 1
    fi
    sleep 0.5
done

# Check routes
check_route() {
    local path="$1"
    local expected="$2"
    local label="$3"

    local response
    response=$(curl -s -L "$BASE_URL$path" 2>/dev/null)

    if echo "$response" | grep -qi "$expected"; then
        echo "[PASS] $label ($path)"
    else
        echo "[FAIL] $label ($path) — expected '$expected' in response"
        FAILED=1
    fi
}

echo ""
echo "=== Route Checks ==="

check_route "/" "EAY" "Homepage"
check_route "/products" "product" "Products page"
check_route "/contact" "contact" "Contact page"
check_route "/about" "about" "About page"
check_route "/faq" "faq" "FAQ page"

echo ""
if [ $FAILED -eq 0 ]; then
    echo "=== ALL CHECKS PASSED ==="
    exit 0
else
    echo "=== SOME CHECKS FAILED ==="
    exit 1
fi
