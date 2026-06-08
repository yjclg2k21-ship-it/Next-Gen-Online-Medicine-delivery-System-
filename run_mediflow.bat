@echo off
TITLE Mediflow: Clinical Pharmacy Ecosystem
echo --------------------------------------------------
echo [1/3] Starting Mediflow Clinical Backend...
echo --------------------------------------------------
start /B php -S localhost:8000 router.php > backend/php_server.log 2>&1

echo [2/3] Verifying Database Connection...
php backend/public/db_check.php

echo [3/3] Launching Mediflow Frontend...
start http://localhost:8000/
echo --------------------------------------------------
echo Mediflow is now running!
echo Backend: http://localhost:8000
echo Frontend Access: http://localhost:8000/ (via Router)
echo --------------------------------------------------
echo Press Ctrl+C to stop servers.
pause
