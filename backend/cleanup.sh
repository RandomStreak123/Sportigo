#!/bin/bash
cd /home/ajith/Project3/backend
ddev mysql -e "DELETE FROM users WHERE username='testuser123';"
echo "Test user removed"
