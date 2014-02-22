#!/bin/bash
# - bin/release.sh
#       this script must be run from project root directory !
#

set -e
BASEDIR=$(dirname $0)

# Version increment, TAG?
version_increment='patch'
if [[ ! -z "$1" ]]; then
	if [ "$1" == "patch" ] || [ "$1" == "minor" ] || [ "$1" == "major" ]; then
		version_increment="$1"
	else
		pms_error "Version increment, if specified should match patch|minor|major."
		exit
	fi
fi
echo -e "Getting next version ..."
semver inc "$version_increment"
TAG=`semver tag|sed "s/v//"`
echo -e "Releasing version $TAG"

# Build phar
echo "Building version $TAG"
box -v build

# Update dist/manifest.json
echo "Updating manifest"
php ./bin/update-manifest.php $TAG

# Updating .version
echo - "Updating .version"
echo $TAG > ".version"

# Commit
echo -e "Commit files"
git add .
git commit -m "Releasing $TAG version"

# Tag & build master branch
echo "Creating tag $TAG"
git checkout master
git tag ${TAG}
git tag -a $TAG -m 'Created tag for version $TAG'