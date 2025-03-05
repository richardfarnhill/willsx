@echo off
echo Running WillsX theme sync...
powershell.exe -ExecutionPolicy Bypass -File "%~dp0scripts\sync-theme.ps1"
pause 