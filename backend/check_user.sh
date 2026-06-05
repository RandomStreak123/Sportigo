#!/bin/bash
TOKEN="143|OYG7jcVU7uLxFu8DlmyEZ1FjvbVQ4X2GtudTyBSt1e94dd95"
curl -sk https://playconnect-backend.ddev.site/api/user \
  -H "Authorization: Bearer $TOKEN" | python3 -c "
import sys, json
d = json.load(sys.stdin)
print('profilePhotoUrl:', d.get('profilePhotoUrl'))
print('profilePicture:', d.get('profilePicture'))
print('profile_picture:', d.get('profile_picture'))
print('profile_photo:', d.get('profile_photo'))
"
