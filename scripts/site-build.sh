#!/bin/bash

echo "Xsmind site build started..."
echo "Dropping Schema..."

drush sql-drop --yes
echo "Importing database SQL file."
drush sql-cli < database/xsmind.sql
echo "Sql dump Imported..."
echo ""
echo "Clearing Cache"
drush cache-rebuild
echo ""
echo "importing configuration."
drush config-import --yes

echo "Clearing Cache"
drush cache-rebuild
