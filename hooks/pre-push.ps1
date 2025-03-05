#!/usr/bin/env powershell
#
# Pre-push hook to sync theme files to local WordPress installation
# This hook is called before a 'git push' is executed

Write-Host "Running pre-push hook to sync theme files..."
Copy-Item -Path "C:\Users\richa\Dev Projects\projects\WillsX\willsx\src\themes\willsx\*" -Destination "C:\Users\richa\Local Sites\willsx\app\public\wp-content\themes\willsx\" -Recurse -Force
Write-Host "Theme files synced successfully!"

# Return 0 to allow the push to proceed
exit 0 