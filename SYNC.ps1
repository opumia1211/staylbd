# ============================================================
#   STAYLBD PROJECT - TEAM SYNC SCRIPT
#   Usage: .\SYNC.ps1
#   Works for ALL team members - push your changes, pull others
# ============================================================

param(
    [string]$Message = ""
)

# --- Check Git identity ---
$GIT_NAME  = (git config user.name  2>$null)
$GIT_EMAIL = (git config user.email 2>$null)

if (-not $GIT_NAME) {
    Write-Host ""
    Write-Host ">>> Git identity not set. Please enter your details:" -ForegroundColor Yellow
    $GIT_NAME  = Read-Host "  Your name (e.g. Sajal)"
    $GIT_EMAIL = Read-Host "  Your email (e.g. sajal@gmail.com)"
    git config user.name  $GIT_NAME
    git config user.email $GIT_EMAIL
    Write-Host "  Identity saved." -ForegroundColor Green
}

Write-Host ""
Write-Host "============================================================" -ForegroundColor Cyan
Write-Host "   STAYLBD PROJECT - TEAM SYNC" -ForegroundColor Cyan
Write-Host "   User: $GIT_NAME" -ForegroundColor Cyan
Write-Host "============================================================" -ForegroundColor Cyan
Write-Host ""

# --- STEP 1: Check for local changes and stash ---
$LocalChanges = (git status --porcelain 2>$null)
$HasChanges = ($null -ne $LocalChanges -and "$LocalChanges".Trim() -ne "")

$StashCreated = $false

if ($HasChanges) {
    Write-Host "[1/4] Saving your local changes temporarily (stash)..." -ForegroundColor Yellow
    $StashLabel = "auto-stash-$(Get-Date -Format 'yyyy-MM-dd-HH-mm')"
    $StashBefore = (git stash list 2>$null | Measure-Object -Line).Lines
    git stash push --include-untracked -m $StashLabel 2>&1 | Out-Null
    $StashAfter  = (git stash list 2>$null | Measure-Object -Line).Lines
    if ($StashAfter -gt $StashBefore) {
        $StashCreated = $true
        Write-Host "      OK - local changes saved." -ForegroundColor Green
    } else {
        Write-Host "      Nothing to stash (already clean)." -ForegroundColor Gray
    }
} else {
    Write-Host "[1/4] No local changes to save." -ForegroundColor Gray
}

Write-Host ""

# --- STEP 2: Fetch and rebase from GitHub ---
Write-Host "[2/4] Downloading latest updates from GitHub..." -ForegroundColor Yellow
git fetch origin main 2>$null | Out-Null
git rebase origin/main 2>&1
$RebaseCode = $LASTEXITCODE

if ($RebaseCode -ne 0) {
    Write-Host ""
    Write-Host "CONFLICT: Same file edited by multiple people!" -ForegroundColor Red
    Write-Host ""
    Write-Host "To fix:" -ForegroundColor Yellow
    Write-Host "  1. Open conflicted files and resolve them." -ForegroundColor White
    Write-Host "  2. Run: git add ." -ForegroundColor White
    Write-Host "  3. Run: git rebase --continue" -ForegroundColor White
    Write-Host "  4. Run SYNC.ps1 again." -ForegroundColor White
    Write-Host ""
    git status
    pause
    exit 1
}

Write-Host "      OK - latest team updates downloaded." -ForegroundColor Green
Write-Host ""

# --- STEP 3: Restore stashed changes ---
if ($StashCreated) {
    Write-Host "[3/4] Restoring your local changes..." -ForegroundColor Yellow
    git stash pop 2>&1 | Out-Null
    $StashCode = $LASTEXITCODE

    if ($StashCode -ne 0) {
        Write-Host ""
        Write-Host "CONFLICT: Your changes conflict with a teammate's changes!" -ForegroundColor Red
        Write-Host "Resolve the conflicts manually, then run SYNC.ps1 again." -ForegroundColor Yellow
        git status --short
        pause
        exit 1
    }
    Write-Host "      OK - your changes restored." -ForegroundColor Green
} else {
    Write-Host "[3/4] No stash to restore." -ForegroundColor Gray
}

Write-Host ""

# --- STEP 4: Commit and push to GitHub ---
$FinalChanges = (git status --porcelain 2>$null)
$HasFinalChanges = ($null -ne $FinalChanges -and "$FinalChanges".Trim() -ne "")

if ($HasFinalChanges) {
    Write-Host "[4/4] Uploading your changes to GitHub..." -ForegroundColor Yellow

    if ($Message -eq "") {
        $Timestamp = Get-Date -Format "yyyy-MM-dd HH:mm"
        $CommitMsg = "Update by $GIT_NAME on $Timestamp"
    } else {
        $CommitMsg = $Message
    }

    git add .
    git commit -m $CommitMsg
    git push origin main

    if ($LASTEXITCODE -eq 0) {
        Write-Host "      OK - Successfully uploaded to GitHub!" -ForegroundColor Green
    } else {
        Write-Host "ERROR: Push failed. Check your internet connection." -ForegroundColor Red
        pause
        exit 1
    }
} else {
    Write-Host "[4/4] Nothing new to upload." -ForegroundColor Gray
}

Write-Host ""
Write-Host "============================================================" -ForegroundColor Green
Write-Host "   SYNC COMPLETE! Everything is up to date." -ForegroundColor Green
Write-Host "============================================================" -ForegroundColor Green
Write-Host ""
Write-Host "Last 3 commits:" -ForegroundColor Cyan
git log --oneline -3
Write-Host ""
pause
