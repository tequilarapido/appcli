* Download codecept.phar to project root directory
    wget http://codeception.com/codecept.phar -O codecept.phar

* Create wp_v381 database on localhost (user:root, password:root)
    mysql -uroot -proot -h"$DB_HOST" --execute="CREATE DATABASE \`wp_v381\` /*!40100 CHARACTER SET utf8 COLLATE 'utf8_general_ci' */;"

* Install and run mailcatcher
    gem install mailcatcher
    mailcatcher