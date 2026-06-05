#!/bin/bash
echo "=== Testing /api/matches ==="
TOKEN=$(curl -sk -X POST https://playconnect-backend.ddev.site/api/login \
  -H "Content-Type: application/json" \
  -d '{"username":"Devan","password":"24681000"}' | python3 -c "import sys,json; d=json.load(sys.stdin); print(d['access_token'])")

echo "Token: $TOKEN"
echo ""
echo "=== Matches response ==="
curl -sk https://playconnect-backend.ddev.site/api/matches \
  -H "Authorization: Bearer $TOKEN" | python3 -m json.tool 2>/dev/null | head -80

echo ""
echo "=== Activities response ==="
curl -sk https://playconnect-backend.ddev.site/api/activities \
  -H "Authorization: Bearer $TOKEN" | python3 -m json.tool 2>/dev/null | head -40
