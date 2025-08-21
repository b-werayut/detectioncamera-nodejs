@echo off
echo Stopping nginx if it's already running...
taskkill /F /IM nginx.exe >nul 2>&1

echo Testing nginx configuration...
nginx.exe -t
IF ERRORLEVEL 1 (
    echo ❌ Error found in nginx configuration.
    timeout /t 5 >nul
    goto end
)

echo Starting nginx...
start "" nginx.exe

echo nginx is now running.

:end
timeout /t -1 >nul
