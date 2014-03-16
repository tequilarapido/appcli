---
layout: page
title: "db:replace"
category: command
date: 2014-03-16 17:57:20
order: 5
---


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

```json
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
```


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

