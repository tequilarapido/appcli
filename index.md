---
layout: default
title: "home"
---

[![Build Status](https://travis-ci.org/tequilarapido/appcli.png?branch=develop)](https://travis-ci.org/tequilarapido/appcli)
[![Scrutinizer Quality Score](https://scrutinizer-ci.com/g/tequilarapido/appcli/badges/quality-score.png?s=312eb20fd70ec286ca086c8f55c2679c5ac3d040)](https://scrutinizer-ci.com/g/tequilarapido/appcli/)
[![Dependency Status](https://www.versioneye.com/user/projects/530b6d34ec1375e93b00007a/badge.png)](https://www.versioneye.com/user/projects/530b6d34ec1375e93b00007a)


### What for ?

`appcli` was created to speed up some tasks needed to deal with deploying Wordpress applications on multiple environements,
like moving application database from staging to production.

The executable can be used in shell scripts to automate those kind of tasks, or handed to IT / Hosting teams responsible for deployments when you have not access to production environement.


appcli expects a configuration file to work properly. The configuration are validated against this [JSON Schema](https://github.com/tequilarapido/appcli/blob/master/res/cli-schema.json).


### Background

The project is build using `Symfony/Console` component, and relay on Laravel `Illuminate/Database` for manipulating database.


### Tests

This project is tested using codeception.


