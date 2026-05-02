@echo off
REM Batch file to run the expired food cleanup script
REM Usage: Run this batch file via Windows Task Scheduler

cd /d "C:\xampp\htdocs\biteback"
"C:\xampp\php\php.exe" cleanup_expired_food.php

REM Optional: Log the output
REM "C:\xampp\php\php.exe" cleanup_expired_food.php >> cleanup_log.txt 2>&1

echo Cleanup completed at %date% %time% >> cleanup_log.txt