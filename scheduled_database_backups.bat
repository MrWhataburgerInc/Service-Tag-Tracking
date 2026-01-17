@echo off
setlocal enabledelayedexpansion

REM Set variables
set MYSQL_PATH=C:\xampp\mysql\bin
set DB_USER=root
set DB_PASSWORD=
set DB_NAME=tech_db
set BACKUP_PATH=C:\xampp\backups

REM Create backup with timestamp
for /f "tokens=2-4 delims=/ " %%a in ('date /t') do (set mydate=%%a_%%b_%%c)

set BACKUP_FILE=%BACKUP_PATH%\backup_%mydate%.sql

REM Run mysqldump
%MYSQL_PATH%\mysqldump.exe -u %DB_USER% -p%DB_PASSWORD% %DB_NAME% > %BACKUP_FILE%

REM Optional: Delete backups older than 30 days
forfiles /S /D +30 /P %BACKUP_PATH% /M *.sql /C "cmd /c del @path"

echo Backup completed: %BACKUP_FILE%



::Make a backups folder
::Press Win + R, type taskschd.msc and hit Enter
::Click "Create Basic Task" on the right side
::Name it (e.g., "Database Backup")
::Choose your trigger (daily, weekly, etc.)
::For "Action," select "Start a program"
::Browse to your scheduled_database_backups.bat file
::Click Finish //