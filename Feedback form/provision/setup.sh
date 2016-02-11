#!/bin/bash

echo "Provisioning virtual machine..."
sudo locale-gen UTF-8
export LANGUAGE=en_US.UTF-8
export LANG=en_US.UTF-8
export LC_ALL=en_US.UTF-8
locale-gen en_US.UTF-8
dpkg-reconfigure locales

echo "Updating Ubuntu OS"
sudo apt-get update -y

echo "Installing Nginx"
sudo apt-get install nginx -y

echo "Updating PHP repository"
sudo apt-get install python-software-properties build-essential
sudo add-apt-repository ppa:ondrej/php5
sudo apt-get update -y

echo "Installing PHP"
sudo apt-get install php5-common php5-dev php5-cli php5-fpm -y

echo "Installing PHP extensions"
sudo apt-get install curl php5-curl php5-gd php5-mcrypt php5-mysql -y

echo "Setting DebConf"
sudo apt-get install debconf-utils -y

echo "Installing MySQL and setting password secret"
debconf-set-selections <<< "mysql-server mysql-server/root_password password 1234"
debconf-set-selections <<< "mysql-server mysql-server/root_password_again password 1234"
sudo apt-get install mysql-server -y

echo "Installing phpmyadmin and setting password secret"
debconf-set-selections <<< "phpmyadmin phpmyadmin/dbconfig-install boolean true"
debconf-set-selections <<< "phpmyadmin phpmyadmin/app-password-confirm password 1234"
debconf-set-selections <<< "phpmyadmin phpmyadmin/mysql/admin-pass password 1234"
debconf-set-selections <<< "phpmyadmin phpmyadmin/mysql/app-pass password 1234"
debconf-set-selections <<< "phpmyadmin phpmyadmin/reconfigure-webserver multiselect none"
sudo apt-get install phpmyadmin -y
sudo ln -s /usr/share/phpmyadmin/ /var/www/pmants

sudo php5enmod mcrypt

echo "Configuring Nginx"
sudo cp /var/www/provision/config/website /etc/nginx/sites-available/website
sudo ln -s /etc/nginx/sites-available/website /etc/nginx/sites-enabled/
sudo rm -rf /etc/nginx/sites-available/default
sudo service nginx restart -y

#echo "Installing other soft"
#sudo apt-get install zip git

#echo "Installing Composer"
#curl -sS https://getcomposer.org/installer | php
#sudo mv composer.phar /usr/local/bin/composer

#echo "Some magic for laravel"
#sudo chown www-data:www-data -R /var/www/test/storage/logs

#echo "Install laravel"
#cd /var/www/src/calculator
#composer create-project --prefer-dist laravel/laravel ../calculator

echo "Importing database data"
cat /var/www/provision/dump.sql | mysql -u root --password=1234

echo "I am out..."