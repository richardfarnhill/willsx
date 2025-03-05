@echo off
echo Installing WillsX Git hooks...
powershell.exe -ExecutionPolicy Bypass -File "%~dp0install-hooks.ps1"
pause 