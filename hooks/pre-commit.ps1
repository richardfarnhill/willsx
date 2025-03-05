# Get the latest version from the remote repository
$remoteVersion = git ls-remote --tags origin | Select-String -Pattern 'v\d+\.\d+\.\d+' | ForEach-Object { $_.Matches.Value } | Sort-Object -Property @{Expression={[version]($_.TrimStart('v'))}} | Select-Object -Last 1

# Get the latest version from the changelog
$changelogVersion = Get-Content DOCUMENTATION/changelog.md | Select-String -Pattern 'v\d+\.\d+\.\d+' | ForEach-Object { $_.Matches.Value } | Sort-Object -Property @{Expression={[version]($_.TrimStart('v'))}} | Select-Object -Last 1

if ($remoteVersion -ne $changelogVersion) {
    Write-Host "ERROR: Version mismatch detected!" -ForegroundColor Red
    Write-Host "Remote version: $remoteVersion" -ForegroundColor Yellow
    Write-Host "Changelog version: $changelogVersion" -ForegroundColor Yellow
    Write-Host "Please update DOCUMENTATION/changelog.md to match the remote version before committing." -ForegroundColor Red
    exit 1
}

# Continue with the commit if versions match
exit 0 