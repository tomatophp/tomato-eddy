echo "Fix Deploy Script"

cd /home/eddy/{{$site->address}}/repository
git reset --hard
git pull origin master
composer update -W
php artisan migrate --force
php artisan config:cache
php artisan optimize:clear
php artisan dusk:install
php artisan up
