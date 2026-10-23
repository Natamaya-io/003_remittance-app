@echo off
set DB=AplikasiRemmitance
set USER=root
set OUT=backup_%DB%_%date:~-4%%date:~4,2%%date:~7,2%.sql
C:\xampp\mysql\bin\mysqldump.exe -u %USER% %DB% > %OUT%
echo Backup dibuat: %OUT%
pause
