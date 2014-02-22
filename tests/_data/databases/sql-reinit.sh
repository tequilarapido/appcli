#!/bin/bash
#
# Tip :
# You can symlink this file at project root :
# ->  ln -s tests/_data/databases/sql-reinit.sh sql-reinit.sh

# Script dir
SCRIPT=$(readlink -f $0)
SCRIPTPATH=`dirname $SCRIPT`

# Dump file
SQLDUMP="$SCRIPTPATH/wp_v381.sql"

# Restoring ...
echo "Restoring database from $SQLDUMP"
mysql -uroot -proot -h127.0.0.1 -e 'DROP DATABASE IF EXISTS `wp_v381`; CREATE DATABASE `wp_v381` /*!40100 CHARACTER SET utf8 COLLATE 'utf8_general_ci' */;';cat  "$SQLDUMP" | mysql -uroot -proot -h127.0.0.1 wp_v381
echo "Done"