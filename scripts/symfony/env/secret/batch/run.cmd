@echo off

setlocal enabledelayedexpansion

set "envFile=.env"

REM ====================================================
REM Generate APP_SECRET if it does not exist
REM ====================================================

set "appSecretExists=0"
for /f "usebackq tokens=1* delims==" %%A in ("%envFile%") do (
    if /i "%%A"=="APP_SECRET" if not "%%B"=="" set "appSecretExists=1"
)

if !appSecretExists! equ 1 (
    echo APP_SECRET already exists, skipping generation
) else (
    for /f "delims=" %%S in ('php -r "echo bin2hex(random_bytes(32));"') do set "APP_SECRET=%%S"
    powershell -Command "(Get-Content '%envFile%') -replace '^APP_SECRET=$', 'APP_SECRET=!APP_SECRET!' | Set-Content '%envFile%'"
    echo APP_SECRET generated and added to .env
)

REM ====================================================
REM Generate HMAC_SECRET if it does not exist
REM ====================================================

set "hmacSecretExists=0"
for /f "usebackq tokens=1* delims==" %%A in ("%envFile%") do (
    if /i "%%A"=="HMAC_SECRET" if not "%%B"=="" set "hmacSecretExists=1"
)

if !hmacSecretExists! equ 1 (
    echo HMAC_SECRET already exists, skipping generation
) else (
    for /f "delims=" %%S in ('php -r "echo bin2hex(random_bytes(32));"') do set "HMAC_SECRET=%%S"
    powershell -Command "(Get-Content '%envFile%') -replace '^HMAC_SECRET=$', 'HMAC_SECRET=!HMAC_SECRET!' | Set-Content '%envFile%'"
    echo HMAC_SECRET generated and added to .env
)

REM ====================================================
REM Generate JWT_PASSPHRASE if it does not exist
REM ====================================================

set "jwtPassphraseExists=0"
for /f "usebackq tokens=1* delims==" %%A in ("%envFile%") do (
    if /i "%%A"=="JWT_PASSPHRASE" if not "%%B"=="" set "jwtPassphraseExists=1"
)

if !jwtPassphraseExists! equ 1 (
    echo JWT_PASSPHRASE already exists, skipping generation
) else (
    for /f "delims=" %%S in ('php -r "echo bin2hex(random_bytes(32));"') do set "JWT_PASSPHRASE=%%S"
    powershell -Command "(Get-Content '%envFile%') -replace '^JWT_PASSPHRASE=$', 'JWT_PASSPHRASE=!JWT_PASSPHRASE!' | Set-Content '%envFile%'"
    echo JWT_PASSPHRASE generated and added to .env
)

REM ====================================================
REM Generate MERCURE_JWT_SECRET if it does not exist
REM ====================================================

set "mercureJwtSecretExists=0"
for /f "usebackq tokens=1* delims==" %%A in ("%envFile%") do (
    if /i "%%A"=="MERCURE_JWT_SECRET" if not "%%B"=="" set "mercureJwtSecretExists=1"
)

if !mercureJwtSecretExists! equ 1 (
    echo MERCURE_JWT_SECRET already exists, skipping generation
) else (
    for /f "delims=" %%S in ('powershell -Command "[Convert]::ToBase64String((1..32 | ForEach-Object { [byte](Get-Random -Max 256) }))"') do set "MERCURE_JWT_SECRET=%%S"
    powershell -Command "(Get-Content '%envFile%') -replace '^MERCURE_JWT_SECRET=$', 'MERCURE_JWT_SECRET=!MERCURE_JWT_SECRET!' | Set-Content '%envFile%'"
    echo MERCURE_JWT_SECRET generated and added to .env
)

endlocal
