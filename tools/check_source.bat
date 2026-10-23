@echo off
setlocal enabledelayedexpansion
set PHP_EXE=C:\xampp\php\php.exe
if not exist "%PHP_EXE%" (
  echo PHP XAMPP tidak ditemukan di %PHP_EXE%
  exit /b 1
)
set FAILED=0
for /R "%~dp0..\app" %%F in (*.php) do (
  "%PHP_EXE%" -l "%%F" >nul || set FAILED=1
)
for /R "%~dp0..\public" %%F in (*.php) do (
  "%PHP_EXE%" -l "%%F" >nul || set FAILED=1
)
if "%FAILED%"=="1" (
  echo Pemeriksaan gagal. Lihat pesan sintaks di atas.
  exit /b 1
)
echo Semua file PHP lolos pemeriksaan sintaks.
exit /b 0
