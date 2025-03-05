# WillsX Theme Sync Script
# This script syncs theme files from the development directory to the local WordPress installation

Write-Host "Syncing WillsX theme files to local WordPress installation..." -ForegroundColor Cyan

# Define paths
$sourcePath = "C:\Users\richa\Dev Projects\projects\WillsX\willsx\src\themes\willsx\*"
$destinationPath = "C:\Users\richa\Local Sites\willsx\app\public\wp-content\themes\willsx\"

# Perform the sync
try {
    Copy-Item -Path $sourcePath -Destination $destinationPath -Recurse -Force
    Write-Host "Theme files synced successfully!" -ForegroundColor Green
} catch {
    Write-Host "Error syncing theme files: $_" -ForegroundColor Red
    exit 1
}

Write-Host "Sync completed at $(Get-Date)" -ForegroundColor Cyan
exit 0 