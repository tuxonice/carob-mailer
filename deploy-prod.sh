#!/bin/sh

# Usage: ./deploy-prod.sh {tag or commit}
# Folder structure
# main-folder
#   |-- releases
#     |-- v0.1.1
#   |-- shared
#     |-- storage

MAIN_PATH=$(pwd)

if [ -z "$1" ]
then
    release_name=$(date +"%Y%m%d_%H%M%S")
else
    release_name=$1
fi

mkdir "releases/$release_name"

cd "releases/$release_name" || exit

git clone https://github.com/tuxonice/carob-mailer.git .
git checkout $release_name

composer install --no-dev
npm install
npm run prod

rm .editorconfig .env.example deploy-prod.sh .gitattributes .gitignore README.md docker-compose.yml phpstan.neon phpunit.xml renovate.json
rm -rf tests .github .git storage

ln -s $MAIN_PATH/shared/.env .env
ln -s $MAIN_PATH/shared/storage storage

cd $MAIN_PATH || exit

cat > cron-run.sh << EOF
#!/bin/sh

php $(pwd)/releases/$release_name/artisan schedule:run
EOF

rm current
ln -s "releases/$release_name/public/" current
