# GitHub-এ সব পরিবর্তন পাঠাতে: প্রজেক্ট রুটে গিয়ে চালান
#   .\sync-github.ps1
# ExecutionPolicy ব্লক করলে:
#   powershell -ExecutionPolicy Bypass -File .\sync-github.ps1

$ErrorActionPreference = 'Stop'
Set-Location $PSScriptRoot

if (Test-Path '.git\index.lock') {
    Remove-Item '.git\index.lock' -Force -ErrorAction SilentlyContinue
}

git checkout main
if ($LASTEXITCODE -ne 0) { exit $LASTEXITCODE }

git pull --rebase origin main
if ($LASTEXITCODE -ne 0) {
    Write-Host "Pull/rebase ব্যর্থ — কনফ্লিক্ট মেটান, তারপর আবার চালান।" -ForegroundColor Red
    exit $LASTEXITCODE
}

git add -A
$stagedFiles = @(git diff --cached --name-only)
if ($stagedFiles.Count -gt 0) {
    $msg = "Team sync: {0:yyyy-MM-dd HH:mm}" -f (Get-Date)
    git commit -m $msg
    if ($LASTEXITCODE -ne 0) { exit $LASTEXITCODE }
} else {
    Write-Host "কমিট করার মতো নতুন পরিবর্তন নেই (শুধু push চেষ্টা হচ্ছে)।" -ForegroundColor DarkYellow
}

git push origin main
if ($LASTEXITCODE -ne 0) { exit $LASTEXITCODE }

Write-Host "GitHub সিঙ্ক সম্পন্ন।" -ForegroundColor Green
