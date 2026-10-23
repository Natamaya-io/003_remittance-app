@echo off

title Fable RemitOps PHP Server

cd /d C:\xampp\htdocs\AplikasiRemmitance

if not exist "C:\xampp\php\php.exe" (
    echo.
    echo ERROR: PHP XAMPP tidak ditemukan.
    echo Lokasi yang diperiksa:
    echo C:\xampp\php\php.exe
    echo.
    pause
    exit /b 1
)

if not exist "public\index.php" (
    echo.
    echo ERROR: public\index.php tidak ditemukan.
    echo Pastikan file ini dijalankan dari folder aplikasi.
    echo.
    pause
    exit /b 1
)

if not exist "public\router.php" (
    echo.
    echo ERROR: public\router.php tidak ditemukan.
    echo Buat router.php terlebih dahulu.
    echo.
    pause
    exit /b 1
)

echo ==========================================
echo Fable RemitOps PHP Development Server
echo ==========================================
echo.
echo PHP       : C:\xampp\php\php.exe
echo Folder    : C:\xampp\htdocs\AplikasiRemmitance\public
echo URL       : http://127.0.0.1:8000
echo.
echo Tekan Ctrl+C untuk menghentikan server.
echo.

"C:\xampp\php\php.exe" ^
    -dxdebug.mode=off ^
    -S 127.0.0.1:8000 ^
    -t "C:\xampp\htdocs\AplikasiRemmitance\public" ^
    "C:\xampp\htdocs\AplikasiRemmitance\public\router.php"

echo.
echo Server dihentikan.
pause