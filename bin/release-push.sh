#!/bin/bash
# - bin/release-push.sh
#       this script must be run from project root directory !
#
set -e
BASEDIR=$(dirname $0)
SCRIPT=$(readlink -f $0)
DIR_SCRIPT=`dirname $SCRIPT`

# Version
TAG=`cat .version`

# Commit
echo -e "Commit files"
git add .
git commit -m "$TAG Release."

# Reforce to last commit
git -f tag ${TAG}

# Push
git push origin master
git push --tags

#
echo "Done."