# XAMPP Safe Stop Script
Write-Host "Safely stopping Apache..." -ForegroundColor Cyan
taskkill /IM httpd.exe /F /T 2>$null

Write-Host "Safely stopping MySQL (Waiting for data flush)..." -ForegroundColor Cyan
# We don't use /F for mysql to allow graceful shutdown
taskkill /IM mysqld.exe 2>$null

Write-Host "XAMPP services stopped safely. You can now close your PC." -ForegroundColor Green
pause
