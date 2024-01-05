@include('tomato-eddy::tasks.apt-functions')

echo "APT Upgrade"

waitForAptUnlock

apt update
apt upgrade -y
