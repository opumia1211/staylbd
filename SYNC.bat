@echo off
echo ========================================
echo   Updating Project from GitHub...
echo ========================================
git pull origin main

echo.
echo ========================================
echo   Uploading your changes to GitHub...
echo ========================================
git add .
git commit -m "Auto-update from team member"
git push origin main

echo.
echo ========================================
echo   Sync Completed!
echo ========================================
pause
