@echo off

set email=ko1962000@gmail.com
set name=chenkohan
set repo_url=https://github.com/chenkohan/VCF.git

if not exist .git (
    git init
    git remote add origin %repo_url%
)

git add .
git commit -m "Initial commit"
git config --global user.email "%email%"
git config --global user.name "%name%"

set remote_branch=main
set local_branch=%remote_branch%

if not exist .git\refs\heads\%local_branch% (
    git checkout -b %local_branch%
)

set error_message=
for /F "tokens=* delims=" %%G in ('git rev-parse %remote_branch%') do set remote_sha=%%G
for /F "tokens=* delims=" %%G in ('git rev-parse %local_branch%') do set local_sha=%%G
if not "%remote_sha%" == "%local_sha%" (
    set error_message=1
    echo The local branch is not synchronized with the remote branch.
    echo Pulling changes from the remote branch...
    git pull origin %remote_branch%
    echo.
)

if "%error_message%" == "" (
    git push -u origin %local_branch%
)

echo Git identity set:
git config --list | findstr user.

pause







