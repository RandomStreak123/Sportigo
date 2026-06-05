#!/bin/bash
curl -X POST -H "Content-Type: application/json" -H "Accept: application/json" -d '{"username": "testuser", "password": "password123", "name": "Test User"}' http://playconnect-backend.ddev.site/api/register -i
