* Download codecept.phar to project root directory
    wget http://codeception.com/codecept.phar -O codecept.phar

* Create wp_v381 database on localhost (user:root, password:root)
    mysql -uroot -proot -h"$DB_HOST" --execute="CREATE DATABASE \`wp_v381\` /*!40100 CHARACTER SET utf8 COLLATE 'utf8_general_ci' */;"

* Install and run mailcatcher
    gem install mailcatcher
    mailcatcher

* For test using mail via sendmail, you have to change your php.ini settings
 * On windows

         SMTP = 127.0.0.1
         smtp_port = 1025
         sendmail_path = "C:\cygwin\bin\catchmail.bat"

 * On Unix

         sendmail_path = "/usr/bin/env /var/lib/gems/1.8/bin/catchmail"


* Run one test

        php codecept.phar run tests/functional/commands/DatabaseConvertToMyISAMCest.php

* Run all tests
        php codecept.phar run functional --steps --env console

