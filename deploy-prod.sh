#!/bin/sh

# Usage: ./deploy-prod.sh
# Folder structure
# main-folder
#   |-- releases
#     |-- 20230819_123000
#   |-- shared
#     |-- storage

release_name=$(date +"%Y%m%d_%H%M%S")
mkdir "releases/$release_name"

cd "releases/$release_name" || exit

git clone https://github.com/tuxonice/carob-mailer.git .
composer install --no-dev
npm install
npm run prod

rm .editorconfig .env.example deploy-prod.sh .gitattributes .gitignore README.md docker-compose.yml phpstan.neon phpunit.xml renovate.json
rm -rf tests .github .git storage

ln -s ../../shared/.env .env
ln -s ../../shared/storage storage

cd ../../

rm current
ln -s "releases/$release_name/public/" current
