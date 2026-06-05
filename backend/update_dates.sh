#!/bin/bash
# Update all match dates to be in the future (shift them all forward by ~2 weeks from now)
cd /home/ajith/Project3/backend

echo "=== Updating match dates to future dates ==="
ddev mysql << 'EOF'
UPDATE sport_matches SET date_time = DATE_ADD(NOW(), INTERVAL 1 DAY) WHERE id = 9;
UPDATE sport_matches SET date_time = DATE_ADD(NOW(), INTERVAL 2 DAY) WHERE id = 10;
UPDATE sport_matches SET date_time = DATE_ADD(NOW(), INTERVAL 3 DAY) WHERE id = 11;
UPDATE sport_matches SET date_time = DATE_ADD(NOW(), INTERVAL 1 DAY) WHERE id = 12;
UPDATE sport_matches SET date_time = DATE_ADD(NOW(), INTERVAL 4 DAY) WHERE id = 13;
UPDATE sport_matches SET date_time = DATE_ADD(NOW(), INTERVAL 5 DAY) WHERE id = 14;
UPDATE sport_matches SET date_time = DATE_ADD(NOW(), INTERVAL 2 DAY) WHERE id = 15;
UPDATE sport_matches SET date_time = DATE_ADD(NOW(), INTERVAL 6 DAY) WHERE id = 16;
UPDATE sport_matches SET date_time = DATE_ADD(NOW(), INTERVAL 3 DAY) WHERE id = 17;
UPDATE sport_matches SET date_time = DATE_ADD(NOW(), INTERVAL 7 DAY) WHERE id = 18;
UPDATE sport_matches SET date_time = DATE_ADD(NOW(), INTERVAL 8 DAY) WHERE id = 19;
UPDATE sport_matches SET date_time = DATE_ADD(NOW(), INTERVAL 4 DAY) WHERE id = 20;
UPDATE sport_matches SET date_time = DATE_ADD(NOW(), INTERVAL 9 DAY) WHERE id = 21;
UPDATE sport_matches SET date_time = DATE_ADD(NOW(), INTERVAL 5 DAY) WHERE id = 22;
UPDATE sport_matches SET date_time = DATE_ADD(NOW(), INTERVAL 10 DAY) WHERE id = 23;
UPDATE sport_matches SET date_time = DATE_ADD(NOW(), INTERVAL 6 DAY) WHERE id = 24;
EOF

echo "=== Verifying updated dates ==="
ddev mysql -e "SELECT id, sport_type, title, date_time, available_slots, max_slots FROM sport_matches ORDER BY date_time;" 2>&1
