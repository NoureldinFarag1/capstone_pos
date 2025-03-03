@echo off
echo Starting PHP Artisan Serve and NPM Run Dev...
start "" "C:\xampp\xampp_start.exe"
start cmd /k "php artisan serve"
start cmd /k "npm run dev 
npm run build"
echo Commands executed successfully.
pause