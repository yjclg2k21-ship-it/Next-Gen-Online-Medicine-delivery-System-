@echo off
REM MediMitra Native Windows Test Harness Executable

echo ------------------------------------------
echo Running Code Syntax Analysis (Linting)...
echo ------------------------------------------
for /R app %%f in (*.php) do php -l "%%f" | findstr /V "No syntax errors"
for /R core %%f in (*.php) do php -l "%%f" | findstr /V "No syntax errors"

echo.
echo ------------------------------------------
echo Executing Core Logic Smoke Tests...
echo ------------------------------------------
php tests/SmokeTest.php

echo.
echo Test Execution Routine Concluded.
pause
