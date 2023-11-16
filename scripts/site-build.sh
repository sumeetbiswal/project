#!/bin/bash

echo "Xsmind site build started..."
echo "Dropping Schema..."

drush sql-drop --yes
echo "Importing xsmind.sql file."
drush sql-cli < database/xsmind.sql
echo "Database Imported..."


echo ""
echo "Clearing Cache.... please wait !"
drush cache-rebuild


echo ""
echo "importing configuration."
drush config-import --yes


echo ""
echo "Updating DB..."
drush updatedb --yes


echo "Clearing Cache"
drush cache-rebuild
