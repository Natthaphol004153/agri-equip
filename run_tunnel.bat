@echo off
setlocal
cd /d "%~dp0"

powershell -NoProfile -ExecutionPolicy Bypass -File "%~dp0run_tunnel.ps1" -DebugMode
set "RC=%ERRORLEVEL%"

if not "%RC%"=="0" (
	echo.
	echo [ERROR] Script failed (code=%RC%)
	echo Check run_tunnel_error.log and cf.log for details
	pause
)

exit /b %RC%