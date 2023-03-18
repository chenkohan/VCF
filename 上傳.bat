@echo off

set /p email=ko1962000@gmail.com: 
set /p name=chenkohan: 

git init
git add .
git commit -m "Initial commit"
git remote add origin https://github.com/chenkohan/VCF
git config --global user.email "%email%"
git config --global user.name "%name%"
git push -u origin main

echo Git identity set:
git config --list | findstr user.

pause






