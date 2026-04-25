$dataDir = "c:\xampp\mysql\data"
$oldDir = "c:\xampp\mysql\data_old"
$backupDir = "c:\xampp\mysql\backup"

Write-Host "Stopping MySQL process..."
Stop-Process -Name mysqld -ErrorAction SilentlyContinue

# Give it a second to release locks
Start-Sleep -Seconds 1

Write-Host "Backing up current data folder..."
if (Test-Path $dataDir) {
    if (Test-Path $oldDir) {
        Write-Host "Removing previous data_old..."
        Remove-Item -Recurse -Force $oldDir
    }
    Rename-Item -Path $dataDir -NewName "data_old"
}

Write-Host "Creating new data folder from backup..."
Copy-Item -Path $backupDir -Destination $dataDir -Recurse

Write-Host "Restoring user databases..."
$exclude = @("mysql", "performance_schema", "phpmyadmin", "test")
if (Test-Path $oldDir) {
    Get-ChildItem -Path $oldDir -Directory | Where-Object { $_.Name -notin $exclude } | ForEach-Object {
        Write-Host "Restoring database: $($_.Name)"
        Copy-Item -Path $_.FullName -Destination $dataDir -Recurse
    }

    Write-Host "Restoring InnoDB metadata (ibdata1)..."
    Copy-Item -Path "$oldDir\ibdata1" -Destination "$dataDir\ibdata1" -Force
}

Write-Host "Fixing complete. Please try starting MySQL from XAMPP Control Panel."
