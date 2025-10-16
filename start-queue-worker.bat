@echo off
echo Starting Laravel Queue Worker for AI Processing...
echo Press Ctrl+C to stop the worker
php artisan queue:work --verbose --tries=3 --timeout=300
pause
