---
layout: page
title: "db:occurrences"
category: command
date: 2014-03-16 18:04:15
order: 6
---


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





