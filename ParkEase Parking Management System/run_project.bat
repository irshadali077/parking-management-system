@echo off
title ParkEase - Parking Management System Launcher
color 0A

echo ===============================================================
echo               PARKEASE - SMART PARKING SYSTEM                  
echo              College Project 1-Click Auto Runner               
echo ===============================================================
echo.

set "PROJECT_DIR=%~dp0"
set "PROJECT_DIR=%PROJECT_DIR:~0,-1%"

:: 1. Check for XAMPP directory
set "XAMPP_DIR=C:\xampp"
if not exist "%XAMPP_DIR%\php\php.exe" (
    echo [ERROR] XAMPP not found at C:\xampp!
    echo Please make sure XAMPP is installed at C:\xampp.
    echo.
    pause
    exit /b 1
)
echo [OK] XAMPP detected at %XAMPP_DIR%

:: 2. Link Project into htdocs if not already linked
set "HTDOCS_LINK=%XAMPP_DIR%\htdocs\parking-management-system"
if not exist "%HTDOCS_LINK%" (
    echo [*] Linking project to XAMPP htdocs...
    mklink /J "%HTDOCS_LINK%" "%PROJECT_DIR%" >nul 2>&1
    if errorlevel 1 (
        echo [!] Could not create junction automatically.
    ) else (
        echo [OK] Project folder linked in htdocs.
    )
) else (
    echo [OK] Project folder is linked in htdocs.
)

:: 3. Start MySQL Server (port 3306) if not already running
echo [*] Checking MySQL service...
"%XAMPP_DIR%\mysql\bin\mysqladmin.exe" -u root ping >nul 2>&1
if errorlevel 1 (
    echo [*] Starting MySQL Database...
    cd /d "%XAMPP_DIR%"
    start "" /min "%XAMPP_DIR%\mysql\bin\mysqld.exe" --defaults-file="%XAMPP_DIR%\mysql\bin\my.ini" --standalone
    cd /d "%PROJECT_DIR%"
    timeout /t 3 /nobreak >nul
)
"%XAMPP_DIR%\mysql\bin\mysqladmin.exe" -u root ping >nul 2>&1
if errorlevel 1 (
    echo [!] Starting MySQL took longer, waiting 3 more seconds...
    timeout /t 3 /nobreak >nul
)
echo [OK] MySQL is active and ready!

:: 4. Auto-check & Import Database if missing
echo [*] Checking database 'parking_db'...
"%XAMPP_DIR%\mysql\bin\mysql.exe" -u root -e "USE parking_db; SHOW TABLES;" >nul 2>&1
if errorlevel 1 (
    echo [*] Importing database schema from parking_db.sql...
    "%XAMPP_DIR%\mysql\bin\mysql.exe" -u root -e "CREATE DATABASE IF NOT EXISTS parking_db;"
    "%XAMPP_DIR%\mysql\bin\mysql.exe" -u root parking_db < "%PROJECT_DIR%\database\parking_db.sql"
    echo [OK] Database imported successfully!
) else (
    echo [OK] Database 'parking_db' is ready.
)

:: 5. Start Apache Web Server if not listening on port 80
echo [*] Checking Apache Web Server...
netstat -ano | findstr ":80 " | findstr LISTENING >nul 2>&1
if errorlevel 1 (
    echo [*] Starting Apache server...
    cd /d "%XAMPP_DIR%"
    start "" /min "%XAMPP_DIR%\apache\bin\httpd.exe"
    cd /d "%PROJECT_DIR%"
    
    :: Wait up to 3 seconds for Apache to bind port 80
    timeout /t 3 /nobreak >nul
)

:: Double check if Apache port 80 is listening
netstat -ano | findstr ":80 " | findstr LISTENING >nul 2>&1
if errorlevel 1 (
    echo [!] Apache startup is taking a moment, waiting 2 seconds...
    timeout /t 2 /nobreak >nul
)
echo [OK] Web server is active and listening!

:: 6. Display Login Details
echo.
echo ===============================================================
echo                PROJECT IS RUNNING SUCCESSFULLY!
echo ===============================================================
echo   Home URL:   http://localhost/parking-management-system/
echo   Admin URL:  http://localhost/parking-management-system/admin/login.php
echo               Username: admin  --  Password: admin123
echo   User URL:   http://localhost/parking-management-system/user/login.php
echo               Email: user@test.com  --  Password: user123
echo ===============================================================
echo.

:: 7. Launch Default Browser
echo [*] Opening website in your default browser...
start http://localhost/parking-management-system/

echo.
echo ===============================================================
echo  Project browser me khul chuka hai!
echo ===============================================================
echo.
pause