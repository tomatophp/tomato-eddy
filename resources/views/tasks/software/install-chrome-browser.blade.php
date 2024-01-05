@include('tomato-eddy::tasks.apt-functions')

echo "Install Chrome Browser"

waitForAptUnlock
cd ~
wget https://dl.google.com/linux/direct/google-chrome-stable_current_amd64.deb
apt install -y ./google-chrome-stable_current_amd64.deb
