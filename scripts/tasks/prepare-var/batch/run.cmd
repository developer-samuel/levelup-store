@echo off

REM ==============================
REM Create required var\ directories
REM ==============================

echo Creating required var\ directories...

IF NOT EXIST "var\cache"    mkdir "var\cache"
IF NOT EXIST "var\log"      mkdir "var\log"
IF NOT EXIST "var\sessions" mkdir "var\sessions"
IF NOT EXIST "var\tmp"      mkdir "var\tmp"
IF NOT EXIST "var\tools"    mkdir "var\tools"

echo Done.
