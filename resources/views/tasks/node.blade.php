echo "Bing Rewards Bot Start"

php {{ $path }}/artisan queue:work --timeout=0 &


