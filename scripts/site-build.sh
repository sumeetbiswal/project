#!/bin/bash

# First running x-debug off command
rm /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini && kill -USR2 $(pgrep -o php-fpm) > /dev/null || /etc/init.d/apache2 reload
tput setaf 1 && echo "Xdebug switched Off" && tput sgr 0 && echo

echo "Xsmind site build started..."
echo "Dropping Schema..."

php /app/vendor/drush/drush/drush sql-drop --yes
echo "Importing xsmind.sql file."
php /app/vendor/drush/drush/drush sql-cli < database/xsmind.sql
echo "Database Imported..."


echo ""
echo "Clearing Cache.... please wait !"
php /app/vendor/drush/drush/drush cache-rebuild


echo ""
echo "importing configuration."
php /app/vendor/drush/drush/drush config-import --yes


echo ""
echo "Updating DB..."
php /app/vendor/drush/drush/drush updatedb --yes


echo "Clearing Cache"
php /app/vendor/drush/drush/drush cache-rebuild
