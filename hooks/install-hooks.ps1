# WillsX Git Hooks Installation Script
# This script installs the Git hooks from the repository to the .git/hooks directory

Write-Host "Installing WillsX Git hooks..." -ForegroundColor Cyan

# Define paths
$repoRoot = Split-Path -Parent $PSScriptRoot
$hooksDir = Join-Path $repoRoot "hooks"
$gitHooksDir = Join-Path $repoRoot ".git\hooks"

# Copy post-commit hook
$postCommitSource = Join-Path $hooksDir "post-commit"
$postCommitDest = Join-Path $gitHooksDir "post-commit"
Copy-Item -Path $postCommitSource -Destination $postCommitDest -Force
Write-Host "Installed post-commit hook" -ForegroundColor Green

# Copy pre-push hook
$prePushSource = Join-Path $hooksDir "pre-push"
$prePushDest = Join-Path $gitHooksDir "pre-push"
Copy-Item -Path $prePushSource -Destination $prePushDest -Force
Write-Host "Installed pre-push hook" -ForegroundColor Green

Write-Host "Git hooks installed successfully!" -ForegroundColor Cyan
Write-Host "These hooks will automatically sync theme files after commits and before pushes." -ForegroundColor Cyan

exit 0 