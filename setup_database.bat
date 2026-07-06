@echo off
echo ========================================
echo Setup Database PostgreSQL untuk TNA
echo ========================================
echo.

echo Membuat database PostgreSQL...
echo Pastikan PostgreSQL sudah terinstall dan service berjalan
echo.

REM Cek apakah psql tersedia
where psql >nul 2>nul
if %ERRORLEVEL% NEQ 0 (
    echo ERROR: PostgreSQL tidak ditemukan di PATH
    echo Pastikan PostgreSQL sudah terinstall dan ditambahkan ke PATH
    pause
    exit /b 1
)

echo Membuat database training_need_analysis...
psql -U postgres -c "CREATE DATABASE training_need_analysis WITH OWNER = postgres ENCODING = 'UTF8';"

if %ERRORLEVEL% EQU 0 (
    echo Database berhasil dibuat!
    echo.
    echo Menjalankan migrasi Laravel...
    php artisan migrate --seed
    
    if %ERRORLEVEL% EQU 0 (
        echo.
        echo ========================================
        echo Setup database berhasil!
        echo Database: training_need_analysis
        echo Host: 127.0.0.1
        echo Port: 5432
        echo Username: postgres
        echo ========================================
    ) else (
        echo ERROR: Migrasi gagal!
    )
) else (
    echo ERROR: Gagal membuat database!
    echo Pastikan PostgreSQL berjalan dan user postgres dapat diakses
)

echo.
pause