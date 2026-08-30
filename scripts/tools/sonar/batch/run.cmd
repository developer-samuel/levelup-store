@echo off

REM ==============================
REM Main entrypoint for SonarQube
REM ==============================

if not exist "var\tools\eslint" mkdir "var\tools\eslint"
echo Generating ESLint report...
npm run lint:report

echo Running SonarQube analysis...

docker run --rm ^
    --network levelup-store_app-network ^
    -e SONAR_HOST_URL=%SONAR_HOST% ^
    -e SONAR_TOKEN=%SONAR_TOKEN% ^
    -v "%cd%:/usr/src" ^
    sonarsource/sonar-scanner-cli:latest ^
    -Dproject.settings=sonar-project.properties ^
    -Dsonar.projectKey=levelup-store ^
    -Dsonar.host.url=%SONAR_HOST%

echo Analysis complete!
