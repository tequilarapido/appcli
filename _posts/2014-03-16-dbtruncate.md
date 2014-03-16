---
layout: page
title: "db:truncate"
category: command
date: 2014-03-16 18:04:00
order: 3
---



This command truncate database tables.

#### Config file

The config for this command goes under `cleanup.truncate` section.
We can specify two operations types:

* simple : An array of tables. The command will truncate given tables as is.
* multi : An array of multisite tables. The command will use the prefix to truncate all the
 multi tables corresponding to the given one. So if we specify `posts`, and the prefix is `wp_`,
 truncate operations will be performed on all tables with names matching `/^wp_([0-9]+_)?posts$/` regex.

```json
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
```

#### Command

    appcli db:truncate /path/to/config-file.json

####  Result example

    Truncating wp_comments ...
    Truncating wp_posts ...
    Truncating wp_1000_comments ...
    Truncating wp_1000_posts ...

    Database size: before=1Mb -> after=1Mb > Gain : 0Mb (0%)

    Took about 0 min. ( 0 sec.)



