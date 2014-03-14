<?php
use Tequilarapido\Cli\Application;
use Tequilarapido\Cli\Commands;
use Tequilarapido\Cli\EnhancedOutput;

// Application directory
define('APP_DIR', __DIR__);
define('CLI_SCHEMA_FILE', APP_DIR . '/res/cli-schema.json');

// Autoload
require_once 'src/vendor/autoload.php';

// Environnement variables from .env file ?
// This is mainly used for local environnement, and to set LAUNCHER (console.php or built phar)
// On travis both launcher will be used for tests
if (is_file(APP_DIR . '/.env')) {
    Dotenv::load(__DIR__);
}

// Console application
$application = new Application('TEQUILARAPIDO APPCLI', '@git-version@');

// Add self-update command
$manifestURL = 'https://github.com/tequilarapido/appcli/raw/master/dist/manifest.json';
$application->addUpdateCommand($manifestURL);

// Commands
$application->addCommands(array(
    new Commands\Infos,
    new Commands\Maintenance,
    new Commands\DatabaseConvertToInnodb,
    new Commands\DatabaseConvertToUtf8,
    new Commands\DatabaseTruncateCleanup,
    new Commands\DatabaseDeleteCleanup,
    new Commands\DatabaseReplace,
    new Commands\DatabaseOccurrences(),
));

// run console
$application->run(null, new EnhancedOutput);