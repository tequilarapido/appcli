#!/bin/bash
# - bin/release-prepare.sh
#       this script must be run from project root directory !
#

BASEDIR=$(dirname $0)
DIR_SCRIPT=$(dirname ${BASH_SOURCE[0]})
DIR_PHAR=`realpath "$DIR_SCRIPT/../dist/downloads"`




# Version increment, TAG?
version_increment='patch'
if [[ ! -z "$1" ]]; then
	if [ "$1" == "patch" ] || [ "$1" == "minor" ] || [ "$1" == "major" ]; then
		version_increment="$1"
	else
		echo  "ERROR - Version increment, if specified should match patch|minor|major."
		exit
	fi
fi
echo  "Getting next version ..."
semver inc "$version_increment"
TAG=`semver tag|sed "s/v//"`

# Backup last version for phar update tests
pharfile="$DIR_PHAR/appcli.phar"
previousPharFile="$DIR_PHAR/appcli-previousversion.phar"
if [ -f "$pharfile" ]; then
    echo  "Copying phar ..."
    cp -fr "$pharfile" "$previousPharFile"
fi

# Tagging version in git, so box will pickup the right version
echo  "Tagging version $TAG"
git tag ${TAG}

# Build phar
echo "Building version $TAG"
box -v build

# Update dist/manifest.json
echo "Updating manifest"
php ./bin/update-manifest.php $TAG

# Updating .version
echo - "Updating .version"
echo $TAG > ".version"

# End.
echo "Done. You must commit/push changes manually after verification."