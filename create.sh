#!/bin/bash
echo "# Web-programing" >> README.md
git init
git add README.md
git commit -m "first commit"
git branch -M main
git remote add origin git@github.com:minhlongmt183/Web-programing.git
git push -u origin main
