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
git push -u origin main

echo Git identity set:
git config --list | findstr user.

pause







