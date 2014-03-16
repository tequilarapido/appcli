---
layout: page
title: "maintenance"
category: command
date: 2014-03-16 18:03:38
order: 0
---

This command can be helpful to take website down or to bring it up.
It can be used before and after commands that take time to be executed.

This command just create or remove a `.maintenance` file on the server, the logic of maintenance must be
implemented by the website according the existance or not of this file.


##### Put website on maintenance

    appcli maintenance on

##### Bring up the website

    appcli maintenance off

##### Get the current status of maintenance

    appcli maintenance status

