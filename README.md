# tequilarapido/appcli

[![Build Status](https://travis-ci.org/tequilarapido/appcli.png?branch=develop)](https://travis-ci.org/tequilarapido/appcli)
[![Scrutinizer Quality Score](https://scrutinizer-ci.com/g/tequilarapido/appcli/badges/quality-score.png?s=312eb20fd70ec286ca086c8f55c2679c5ac3d040)](https://scrutinizer-ci.com/g/tequilarapido/appcli/)
[![Dependency Status](https://www.versioneye.com/user/projects/530b6d34ec1375e93b00007a/badge.png)](https://www.versioneye.com/user/projects/530b6d34ec1375e93b00007a)


# Installation

TEQUILARAPIDO appcli is a phar. So installation is the same as composer.

## *nix

    # Download latest release directy to /usr/local/bin for easy access and make it executable
    cd /usr/local/bin
    wget --no-check-certificate https://github.com/tequilarapido/appcli/raw/master/dist/downloads/appcli.phar -o appcli
    chmod +x appcli



## Windows (cygwin compatible)

    # 1/ Create a directory for the app (in c:\ProgramData for instance)
    cd "c:\ProgramData"
    mkdir tequilarapido-appcli
    cd tequilarapido-appcli

    # 2/ Download appcli.phar, appcli and appcli.bat
    # If not on cygwin (just download files using your browser)
    wget --no-check-certificate https://github.com/tequilarapido/appcli/raw/master/dist/downloads/appcli.phar
    wget --no-check-certificate https://github.com/tequilarapido/appcli/raw/master/dist/downloads/install/appcli.bat
    wget --no-check-certificate https://github.com/tequilarapido/appcli/raw/master/dist/downloads/install/appcli

    # 3/ Add created directory to your Windows Environement variable (%PATH%)

    # 4/ Restart your console if opened.

## Test your install

Run the command above, if everything is ok, it will show you the version of the installed appcli.

    appcli --version

![appcli version](http://imgur.com/A1lruYw.png)


# Update

Just run

    appcli self-update

# Commands

##  Maintenance

This command can be helpful to take website down or to bring it up. It can be used before
and after commands that take time to be executed.

This command just create or remove a `.maintenance` file on the server. The logic of maintenance must be
implemented by the website according the existance or not of this file.

    # Put website on maintenance
    appcli maintenance on

    # Bring up the website
    appcli maintenance off

    # Get the current status of maintenance
    appcli maintenance status

## Convert tables to InnoDB

This command convert all table of database given in configuration file to utf8

### Config file

    #Configuration file : /path/to/config-file.json
    {
        "project": "Project name",

        "database": {
            "host": "127.0.0.1",
            "database": "wp_v381",
            "username": "root",
            "password": "root",
            "prefix": "wp_"
        }
    }

### Command

    appcli db:innodb /path/to/config-file.json

###  Result example

    -------------------------------------------------
     Setting database engine to InnoDB
    -------------------------------------------------
     wp_1000_comments
      1/13 [==>-------------------------]   7% wp_1000_posts
      2/13 [====>-----------------------]  15% wp_commentmeta
      3/13 [======>---------------------]  23% wp_comments
      4/13 [========>-------------------]  30% wp_links
      5/13 [==========>-----------------]  38% wp_options
      6/13 [============>---------------]  46% wp_postmeta
      7/13 [===============>------------]  53% wp_posts
      8/13 [=================>----------]  61% wp_term_relationships
      9/13 [===================>--------]  69% wp_term_taxonomy
     10/13 [=====================>------]  76% wp_terms
     11/13 [=======================>----]  84% wp_usermeta
     12/13 [=========================>--]  92% wp_users
     13/13 [============================] 100%
     Done. All tables are now InnoDB

































