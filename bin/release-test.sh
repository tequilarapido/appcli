#!/bin/bash
#
set -e
BASEDIR=$(dirname $0)
SCRIPT=$(readlink -f $0)
DIR_SCRIPT=`dirname $SCRIPT`

php dist/downloads/appcli.phar --version