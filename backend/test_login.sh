#!/bin/bash
echo "=== Testing Devan login with password 24681000 ==="
curl -sk -X POST https://playconnect-backend.ddev.site/api/login \
  -H "Content-Type: application/json" \
  -d '{"username":"Devan","password":"24681000"}' | python3 -m json.tool 2>/dev/null || echo "Raw response received"

echo ""
echo "=== Testing Devan login with password 'password' ==="
curl -sk -X POST https://playconnect-backend.ddev.site/api/login \
  -H "Content-Type: application/json" \
  -d '{"username":"Devan","password":"password"}' | python3 -m json.tool 2>/dev/null || echo "Raw response received"

echo ""
echo "=== Testing Ajith login ==="
curl -sk -X POST https://playconnect-backend.ddev.site/api/login \
  -H "Content-Type: application/json" \
  -d '{"username":"Ajith","password":"24681000"}' | python3 -m json.tool 2>/dev/null || echo "Raw response received"

echo ""
echo "=== Check users table ==="
cd /home/ajith/Project3/backend && ddev mysql -e "SELECT id, name, username, email, LEFT(password,30) as pass_start FROM users;" 2>&1
