@echo off
start /B minimarket.bat
@REM start microsoft-edge:http://127.0.0.1:8000/
start chrome http://127.0.0.1:8000/
cd C:\xampp\htdocs\mozaic_kasih_ibu
php artisan serve