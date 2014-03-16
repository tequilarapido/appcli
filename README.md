# tequilarapido/appcli

[![Build Status](https://travis-ci.org/tequilarapido/appcli.png?branch=develop)](https://travis-ci.org/tequilarapido/appcli)
[![Scrutinizer Quality Score](https://scrutinizer-ci.com/g/tequilarapido/appcli/badges/quality-score.png?s=312eb20fd70ec286ca086c8f55c2679c5ac3d040)](https://scrutinizer-ci.com/g/tequilarapido/appcli/)
[![Dependency Status](https://www.versioneye.com/user/projects/530b6d34ec1375e93b00007a/badge.png)](https://www.versioneye.com/user/projects/530b6d34ec1375e93b00007a)

Read the documentation : [http://tequilarapido.github.io/appcli/](http://tequilarapido.github.io/appcli/)


## Installation

TEQUILARAPIDO appcli is a phar. So installation is the same as composer.

### *nix

    # Download latest release directy to /usr/local/bin for easy access and make it executable
    cd /usr/local/bin
    wget --no-check-certificate https://github.com/tequilarapido/appcli/raw/master/dist/downloads/appcli.phar -o appcli
    chmod +x appcli



### Windows (cygwin compatible)

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

### Test your install

Run the command above, if everything is ok, it will show you the version of the installed appcli.

    appcli --version

![appcli version](http://imgur.com/A1lruYw.png)


## Update

Just run

    appcli self-update

## Commands

* [maintenance](#command-maintenance)
* [db:innodb](#command-db-innodb)
* [db:utf8](#command-db-utf8)
* [db:truncate](#command-db-truncate)
* [db:delete](#command-db-delete)
* [db:replace](#command-db-replace)
* [db:occurrences](#command-db-occurrences)


<a name="command-maintenance"/>
###  [maintenance] Maintenance

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

<a name="command-db-innodb"/>
### [db:innodb] Convert tables to InnoDB

This command convert all tables to InnoDB

#### Config file

    #Configuration file : /path/to/config-file.json
    {
        "project": "Project name",

        "database": {
            "host": "127.0.0.1",
            "database": "wp_v381",
            "username": "root",
            "password": "----",
            "prefix": "wp_"
        }
    }

#### Command

    appcli db:innodb /path/to/config-file.json

####  Result example

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


<a name="command-db-utf8"/>
### [db:utf8] Convert tables to utf8/utf8_general_ci

This command convert all tables to utf8/utf8_general_ci

#### Config file

    #Configuration file : /path/to/config-file.json
    {
        "project": "Project name",

        "database": {
            "host": "127.0.0.1",
            "database": "wp_v381",
            "username": "root",
            "password": "----",
            "prefix": "wp_"
        }
    }

#### Command

    appcli db:innodb /path/to/config-file.json

####  Result example

    -------------------------------------------------
     Setting database charset to utf8 and collation to utf8_general_ci
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
     Done. All table are now utf8/utf8_general_ci

     Took about 0 min. ( 2 sec.)


<a name="command-db-truncate"/>
### [db:truncate] Truncate database tables

This command truncate database tables.

#### Config file

The config for this command goes under `cleanup.truncate` section.
We can specify two operations types:

* simple : An array of tables. The command will truncate given tables as is.
* multi : An array of multisite tables. The command will use the prefix to truncate all the
 multi tables corresponding to the given one. So if we specify `posts`, and the prefix is `wp_`,
 truncate operations will be performed on all tables with names matching `/^wp_([0-9]+_)?posts$/` regex.


    {
        "project": "Wordpress 3.8.1",
    
        "database": {
            "host": "127.0.0.1",
            "database": "wp_v381",
            "username": "root",
            "password": "----",
            "prefix": "wp_"
        },
    
        "cleanup": {
            "truncate": {
                "simple": [
                    "wp_comments",
                    "wp_posts"
                ],
                "multi": [
                    "comments",
                    "posts"
                ]
            }
        }
    
    }

#### Command

    appcli db:truncate /path/to/config-file.json

####  Result example

    Truncating wp_comments ...
    Truncating wp_posts ...
    Truncating wp_1000_comments ...
    Truncating wp_1000_posts ...

    Database size: before=1Mb -> after=1Mb > Gain : 0Mb (0%)

    Took about 0 min. ( 0 sec.)



<a name="command-db-delete"/>
### [db:delete] Delete database records

This command can be used, to reduce database size by deleting records according
to a condition. For instance, removing wp_posts revisions.


#### Config file

The config for this command goes under `cleanup.delete` section. It is an array
of operations. Each operation is related to a table. the fields are the following :

* table : table name
* multi : For wordpress multi-instance. (see. db:truncate)
* conditions : An array of AND conditions. Each condition is :
 * field : string representing a table column
 * operator : SQL operator (<, >, <>, etc ...)
 * value : value on which the field will be compared against

Behind the scences, conditions will be passed to [Laravel / Query builder]((http://laravel.com/docs/queries#selects)) `where` clause.

     foreach ($tableOperations->conditions as $cond) {
        $dbTable->where($cond->field, $cond->operator, $cond->value);
     }


Config example

    "cleanup": {

        "delete": [
            {
                "table": "posts",
                "multi": true,
                "conditions": [
                    {
                        "field": "post_type",
                        "operator": "!=",
                        "value": "unkown_type"
                    },
                    {
                        "field": "post_date",
                        "operator": "<",
                        "value": "2025"
                    }
                ]
            },
            {
                "table": "wp_comments",
                "multi": false,
                "conditions": [
                    {
                        "field": "comment_date",
                        "operator": "<",
                        "value": "2025"
                    }
                ]
            }
        ]

    }

#### Command

    appcli db:delete /path/to/config-file.json

#### Result example

     Deleting items from table wp_1000_posts ...
          -> [{"field":"post_type","operator":"!=","value":"unkown_type"},{"field":"post_date","operator":"<","value":"2025"}]
     Deleting items from table wp_posts ...
          -> [{"field":"post_type","operator":"!=","value":"unkown_type"},{"field":"post_date","operator":"<","value":"2025"}]
     Deleting items from table wp_comments ...
          -> [{"field":"comment_date","operator":"<","value":"2025"}]

     Database size: before=1Mb -> after=1Mb > Gain : 0Mb (0%)

     Took about 0 min. ( 0 sec.)


<a name="command-db-replace"/>
### [db:replace] Replace strings in database

This command can be used, to search and replace strings inside database.
It will replace safely string in serialized PHP or Json objects.
We use this command to port wordpress database from a domain to another as part of our workflow
for deploying a wordpress application on multiple environements (local, staging, production ...).

> Warning : replace informations will be performed on database. So backup your database before running this command.

#### Config file

this command uses multiple sections on the config file.

##### Database informations

this goes under `databaseè  section

    "database": {
        "host": "127.0.0.1",
        "database": "wp_v381",
        "username": "root",
        "password": "---",
        "prefix": "wp_"
    },

##### Notification informations

this can be used for other commands. if specified, the settings will be used to send
a notification mail at the end of the command. This is handful, if this command is run by another person, maybe IT / hosting team,
and you want to have result right away, or to share this information with your team.

    "notify": {
        "from": "notify@appcli-example.com",
        "to": [
            "team@work.com"
        ],
        "transport": {
            "type": "smtp",
            "parameters": {
                "host": "127.0.0.1",
                "port": 1025
            }
        }
    },


The notification will be sent, only if `replace.notify` is true. (see below)

* Transport type :
As transport type, you can choose `smtp` and specify `parameters`,
or specify `sendmail`, and leave parameters empty.




##### Search / Replace informations

this goes under `replace` section.

* Replacements :
You can specify here multiple {from, to} replacements.
* Notify : if true, notification will be sent.
* excludeTables : An array of table to excludes while searching for `replacement.from`
occurrences.


    "replace": {
        "replacements": [
            {
                "from": "wordpress-381.dev",
                "to": "www.wordpress-381.com"
            },
            {
                "from": "another-domain.dev",
                "to": "another-domain.com"
            },
        ],
        "notify": true,
        "excludeTables": [
        ]
    }


#### Command

The command accept as option `--use-transactions`. This wrap all the sql update statements
into transactions for each table. this can dramatically reduce the time needed to perform the db:replace operation
on big databases.

This is to be prefered even for small databases.


    appcli db:replace /path/to/config-file.json

    # using transactions
    appcli db:replace --use-transactions /path/to/config-file.json


#### Result example

    Analysing database : looking for text columns ...
      1/12 [==>-------------------------]   8% : Processing wp_1000_comments
                                             -> 0 queries
      2/12 [====>-----------------------]  16% : Processing wp_1000_posts
                                             -> 3 queries
      3/12 [=======>--------------------]  25% : Processing wp_commentmeta
                                             -> 0 queries
      4/12 [=========>------------------]  33% : Processing wp_comments
                                             -> 0 queries
      5/12 [===========>----------------]  41% : Processing wp_links
                                             -> 0 queries
      6/12 [==============>-------------]  50% : Processing wp_options
                                             -> 5 queries
      7/12 [================>-----------]  58% : Processing wp_postmeta
                                             -> 0 queries
      8/12 [==================>---------]  66% : Processing wp_posts
                                             -> 3 queries
      9/12 [=====================>------]  75% : Processing wp_term_taxonomy
                                             -> 0 queries
     10/12 [=======================>----]  83% : Processing wp_terms
                                             -> 0 queries
     11/12 [=========================>--]  91% : Processing wp_usermeta
                                             -> 0 queries
     12/12 [============================] 100% : Processing wp_users
                                             -> 0 queries

     Total executed queries : 11
     Notification sent.

     Took about 0 min. ( 1 sec.)


<a name="command-db-occurrences"/>
### [db:occurrences] Search occurrences in database

This command works exactly the same as `db:replace`, with the same configurations,
but will print occurrences of searches without replacing anything.

This can be useful, to see if db:replace work correctly, as they will be no occurrences.

#### Command

* Using configuration file : will search for `replacement.from` occurrences

        appcli db:occurrences /path/to/config-file.json

* Passing search items separated by | as second argument :

        appcli  db:occurrences  /path/to/config-file.json "admin@|mystery|newest"


#### Result example

     Analysing database : looking for text columns ...
      1/12 [==>-------------------------]   8% : Processing wp_1000_comments
      2/12 [====>-----------------------]  16% : Processing wp_1000_posts
      3/12 [=======>--------------------]  25% : Processing wp_commentmeta
      4/12 [=========>------------------]  33% : Processing wp_comments
      5/12 [===========>----------------]  41% : Processing wp_links
      6/12 [==============>-------------]  50% : Processing wp_options
      7/12 [================>-----------]  58% : Processing wp_postmeta
      8/12 [==================>---------]  66% : Processing wp_posts
      9/12 [=====================>------]  75% : Processing wp_term_taxonomy
     10/12 [=======================>----]  83% : Processing wp_terms
     11/12 [=========================>--]  91% : Processing wp_usermeta
     12/12 [============================] 100% : Processing wp_users



    -------------------------------------------------
     Occurrences by table
    -------------------------------------------------
     wp_options : 3
     wp_users : 1


    -------------------------------------------------
     Total
    -------------------------------------------------
     Total occurrences : 4

     Took about 0 min. ( 0 sec.)









