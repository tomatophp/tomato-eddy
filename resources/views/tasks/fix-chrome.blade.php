@include('tasks.apt-functions')

echo "Fix Chrome Version"

waitForAptUnlock

apt -y --purge remove google-chrome-stable
cd ~
mkdir chrome16
cd chrome16
wget https://dl.google.com/linux/direct/google-chrome-stable_current_amd64.deb
apt install -y ./google-chrome-stable_current_amd64.deb
supervisorctl restart all
