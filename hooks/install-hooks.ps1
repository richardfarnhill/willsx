# WillsX Git Hooks Installation Script
# This script installs the Git hooks from the repository to the .git/hooks directory

Write-Host "Installing WillsX Git hooks..." -ForegroundColor Cyan

# Define paths
$repoRoot = Split-Path -Parent $PSScriptRoot
$hooksDir = Join-Path $repoRoot "hooks"
$gitHooksDir = Join-Path $repoRoot ".git\hooks"

# Copy post-commit hook
$postCommitSource = Join-Path $hooksDir "post-commit.ps1"
$postCommitDest = Join-Path $gitHooksDir "post-commit.ps1"
Copy-Item -Path $postCommitSource -Destination $postCommitDest -Force
Write-Host "Installed post-commit hook" -ForegroundColor Green

# Create post-commit batch file
$postCommitBat = Join-Path $gitHooksDir "post-commit"
@"
#!/bin/sh
powershell.exe -ExecutionPolicy Bypass -File ".git/hooks/post-commit.ps1"
"@ | Out-File -FilePath $postCommitBat -Encoding ascii -Force
Write-Host "Created post-commit shell script" -ForegroundColor Green

# Copy pre-push hook
$prePushSource = Join-Path $hooksDir "pre-push.ps1"
$prePushDest = Join-Path $gitHooksDir "pre-push.ps1"
Copy-Item -Path $prePushSource -Destination $prePushDest -Force
Write-Host "Installed pre-push hook" -ForegroundColor Green

# Create pre-push batch file
$prePushBat = Join-Path $gitHooksDir "pre-push"
@"
#!/bin/sh
powershell.exe -ExecutionPolicy Bypass -File ".git/hooks/pre-push.ps1"
exit 0
"@ | Out-File -FilePath $prePushBat -Encoding ascii -Force
Write-Host "Created pre-push shell script" -ForegroundColor Green

Write-Host "Git hooks installed successfully!" -ForegroundColor Cyan
Write-Host "These hooks will automatically sync theme files after commits and before pushes." -ForegroundColor Cyan

exit 0 