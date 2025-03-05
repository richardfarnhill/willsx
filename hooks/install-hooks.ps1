# WillsX Git Hooks Installation Script
# This script installs the Git hooks from the repository to the .git/hooks directory

Write-Host "Installing WillsX Git hooks..." -ForegroundColor Cyan

# Get the Git hooks directory
$hooksDir = git rev-parse --git-path hooks
$sourceDir = $PSScriptRoot

# Create backup of existing hooks
if (Test-Path "$hooksDir/pre-commit") {
    Copy-Item "$hooksDir/pre-commit" "$hooksDir/pre-commit.backup"
}
if (Test-Path "$hooksDir/post-commit") {
    Copy-Item "$hooksDir/post-commit" "$hooksDir/post-commit.backup"
}
if (Test-Path "$hooksDir/pre-push") {
    Copy-Item "$hooksDir/pre-push" "$hooksDir/pre-push.backup"
}

# Install pre-commit hook for version checking
$preCommitContent = @"
#!/bin/sh
powershell.exe -ExecutionPolicy Bypass -File "$sourceDir/pre-commit.ps1"
if [ `$? -ne 0 ]; then
    exit 1
fi
"@
Set-Content -Path "$hooksDir/pre-commit" -Value $preCommitContent

# Install post-commit hook for theme syncing
$postCommitContent = @"
#!/bin/sh
echo "Running post-commit hook to sync theme files..."
powershell.exe -ExecutionPolicy Bypass -File "$sourceDir/sync-theme.ps1"
"@
Set-Content -Path "$hooksDir/post-commit" -Value $postCommitContent

# Install pre-push hook for theme syncing
$prePushContent = @"
#!/bin/sh
echo "Running pre-push hook to sync theme files..."
powershell.exe -ExecutionPolicy Bypass -File "$sourceDir/sync-theme.ps1"
"@
Set-Content -Path "$hooksDir/pre-push" -Value $prePushContent

# Make hooks executable
if ($IsLinux -or $IsMacOS) {
    chmod +x "$hooksDir/pre-commit"
    chmod +x "$hooksDir/post-commit"
    chmod +x "$hooksDir/pre-push"
}

Write-Host "Git hooks installed successfully!" -ForegroundColor Green
Write-Host "The following hooks were installed:" -ForegroundColor Yellow
Write-Host "- pre-commit (version check)" -ForegroundColor Yellow
Write-Host "- post-commit (theme sync)" -ForegroundColor Yellow
Write-Host "- pre-push (theme sync)" -ForegroundColor Yellow

exit 0 