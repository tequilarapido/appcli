#!/bin/bash
# - bin/release-push.sh
#       this script must be run from project root directory !
#
set -e
BASEDIR=$(dirname $0)
DIR_SCRIPT=$(dirname ${BASH_SOURCE[0]})

# Version
TAG=`cat .version`

# Commit
echo "Commit files"
git add .
git commit -m "$TAG Release."

# Reforce to last commit
git tag --force ${TAG}

# Push
git push origin master
git push --tags

#
echo "Done."