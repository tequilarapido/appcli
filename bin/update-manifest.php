<?php

// Version file
$version_file = dirname(__DIR__) . '/dist/version';

// Manifest file ?
$manifest_file = dirname(__DIR__) . '/dist/manifest.json';
if (!is_file($manifest_file))
{
    echo "Manifest file not found!" . PHP_EOL;
    exit;
}

// Get version data froms args
$tag = $argv[1];

// Get sha1
$phar = dirname(__DIR__) . "/dist/downloads/appcli.phar";
if (!is_file($phar))
{
    echo "Version phar not found <$phar>!" . PHP_EOL;
    exit;
}
$sha1 = sha1_file($phar);

// New version
$newVersion = array(
    "name"    => "appcli.phar",
    "sha1"    => $sha1,
    "url"     => "https://github.com/tequilarapido/appcli/raw/master/dist/downloads/appcli.phar",
    "version" => $tag,
);

// Store current version as latest in version file
file_put_contents($version_file, $tag);

// current versions
$versions = json_decode(file_get_contents($manifest_file));
array_push($versions, $newVersion);

// Store back pretty json to manifest
require_once dirname(__DIR__) . '/src/vendor/autoload.php';
$formatter     = new Hazbo\Json\Formatter();
$json          = str_replace('\/', '/', json_encode($versions));
$formattedJson = $formatter->format($json);
file_put_contents($manifest_file, $formattedJson);

// Done
echo "Manifest file updated with version " . $tag . PHP_EOL;