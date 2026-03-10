@echo off
echo ========================================
echo Sistem Analisa Kebutuhan Pelatihan
echo Pengadilan Negeri Sleman
echo ========================================
echo.

echo Checking system requirements...

REM Check PHP
php --version >nul 2>&1
if %ERRORLEVEL% NEQ 0 (
    echo ERROR: PHP not found! Please install PHP 8.2+
    pause
    exit /b 1
)

REM Check Composer
composer --version >nul 2>&1
if %ERRORLEVEL% NEQ 0 (
    echo ERROR: Composer not found! Please install Composer
    pause
    exit /b 1
)

echo ✓ PHP and Composer found

echo.
echo Checking database connection...
php artisan tinker --execute="DB::connection()->getPdo(); echo 'Database OK';" >nul 2>&1
if %ERRORLEVEL% NEQ 0 (
    echo ERROR: Database connection failed!
    echo Please check your PostgreSQL configuration in .env file
    pause
    exit /b 1
)

echo ✓ Database connection OK

echo.
echo Running SAW Analysis...
php test_saw.php

echo.
echo ========================================
echo Starting Laravel Development Server...
echo ========================================
echo.
echo Access the system at: http://127.0.0.1:8000
echo.
echo Dashboard Features:
echo - View system statistics
echo - Run SAW analysis
echo - Manage employee data
echo - Input assessments
echo - View training priorities
echo.
echo Press Ctrl+C to stop the server
echo ========================================

php artisan serve