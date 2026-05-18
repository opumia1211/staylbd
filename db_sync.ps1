# StayLBD Master Database Sync Script
# This script updates the Master SQL and Backup SQL from the live database using the Artisan command.

$PhpPath = "C:\xampp\php\php.exe"
$ProjectPath = "C:\xampp\htdocs\staylbd\core"

Set-Location $ProjectPath

Write-Host "Updating Master Database SQL..." -ForegroundColor Cyan
& $PhpPath artisan staylbd:export-master-sql

if ($LASTEXITCODE -eq 0) {
    Write-Host "`nProject Database is now Synchronized." -ForegroundColor Yellow
} else {
    Write-Host "`nError: Database sync failed." -ForegroundColor Red
}

