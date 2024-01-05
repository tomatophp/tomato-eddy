@include('tomato-eddy::tasks.apt-functions')

echo "Reload Caddy"

waitForAptUnlock

server caddy reload
curl https://{{$site->address}}
