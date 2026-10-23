@echo off
set DB=AplikasiRemmitance
set USER=root
set /p FILE=Masukkan path file SQL backup: 
C:\xampp\mysql\bin\mysql.exe -u %USER% %DB% < "%FILE%"
echo Restore selesai.
pause
