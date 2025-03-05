#!/usr/bin/env powershell
#
# Post-commit hook to sync theme files to local WordPress installation
# This hook is called after a successful 'git commit'

Write-Host "Running post-commit hook to sync theme files..."
Copy-Item -Path "C:\Users\richa\Dev Projects\projects\WillsX\willsx\src\themes\willsx\*" -Destination "C:\Users\richa\Local Sites\willsx\app\public\wp-content\themes\willsx\" -Recurse -Force
Write-Host "Theme files synced successfully!"

exit 0 