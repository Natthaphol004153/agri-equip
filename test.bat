@echo off
start cmd /k "php artisan serve"
timeout /t 2 >nul

for /f "tokens=2 delims= " %%A in ('
    cloudflared.exe tunnel --url http://127.0.0.1:8000 ^| findstr "https://"
') do (
    echo %%A
)

pause