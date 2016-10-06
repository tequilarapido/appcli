#!/bin/bash
#
set -e
BASEDIR=$(dirname $0)
DIR_SCRIPT=$(dirname ${BASH_SOURCE[0]})

php dist/downloads/appcli.phar --version