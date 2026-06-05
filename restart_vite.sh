#!/bin/bash
# Kill all existing vite processes and restart
pkill -f "vite" 2>/dev/null
sleep 2
echo "Vite processes killed"

# Start fresh in background
cd /home/ajith/Project3/frontend
nohup npm run dev -- --host > /tmp/vite.log 2>&1 &
sleep 3
echo "Vite restarted. PID: $!"
cat /tmp/vite.log
