@include('tasks.apt-functions')

echo "Install Ubuntu Desktop"

waitForAptUnlock
apt install -y tasksel
apt install -y ubuntu-desktop
apt install -y lightdm
echo "/usr/sbin/lightdm" > /etc/X11/default-display-manager
DEBIAN_FRONTEND=noninteractive DEBCONF_NONINTERACTIVE_SEEN=true dpkg-reconfigure lightdm
echo set shared/default-x-display-manager lightdm | debconf-communicate
systemctl set-default graphical.target
