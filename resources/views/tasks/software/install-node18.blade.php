@include('tasks.apt-functions')

echo "Install Node 18"

waitForAptUnlock
apt-get install -y ca-certificates curl gnupg
mkdir -p /etc/apt/keyrings
curl -fsSL https://deb.nodesource.com/gpgkey/nodesource-repo.gpg.key | gpg --dearmor -o /etc/apt/keyrings/nodesource.gpg
echo "deb [signed-by=/etc/apt/keyrings/nodesource.gpg] https://deb.nodesource.com/node_18.x nodistro main" | tee /etc/apt/sources.list.d/nodesource.list
apt-get update
waitForAptUnlock

apt-get install -y --force-yes nodejs

echo "Install Node Packages"

npm install -g fx gulp n pm2 svgo yarn zx
