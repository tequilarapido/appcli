<?php namespace commands;

use Symfony\Component\Filesystem\Filesystem;
use \TestGuy;

class PharUpdateCest
{
    /**
     * @var Filesystem
     */
    private $fs;

    /**
     * @var string
     */
    private $previousPhar = 'previous.phar';

    /**
     * @var string
     */
    private $currentPhar = 'current.phar';


    /*
    |--------------------------------------------------------------------------
    | Tests
    |--------------------------------------------------------------------------
    |
    |
    |
    */


    /**
     * @param TestGuy $I
     */
    public function update_previous_phar_to_latest_release(TestGuy $I)
    {
        $I->wantTo('run self-update command to update previous phar to lateset release');

        $this->createTestPhars();

        $previousVersion = $this->getPharVersion($this->getTestsPath() . $this->previousPhar);
        $currentVersion = $this->getPharVersion($this->getTestsPath() . $this->currentPhar);
        $previousPharPath = $this->getTestsPath() . $this->previousPhar;

        // update
        $I->wantTo('to update previous phar (' . $previousVersion . ') to latest release (' . $currentVersion . ')');
        $I->runShellCommand("php $previousPharPath self-update");

        $I->dontSeeInShellOutput('Could not open input file:');
        $I->dontSeeInShellOutput('Error');
        $I->dontSeeInShellOutput('Exception');
        $I->dontSeeInShellOutput('Fatal');
        $I->seeInShellOutput('Looking for updates...');
        $I->seeInShellOutput('Update successful!');

        // Check out verion of updated phar
        $I->expect("that the version of the updated phar is <$currentVersion>");
        $I->runShellCommand("php $previousPharPath --version");
        $I->seeInShellOutput($currentVersion);

        // After
        $this->removeTestPhars();
    }

    /**
     * @param TestGuy $I
     */
    public function try_update_latest_release(TestGuy $I)
    {
        $I->wantTo('run self-update command to update aleady up to date phar');
        $this->createTestPhars();

        $currentPharPath = $this->getTestsPath() . $this->currentPhar;
        $currentVersion = $this->getPharVersion($this->getTestsPath() . $this->currentPhar);

        $I->wantTo("to update already updated phar ($currentVersion)");
        $I->expect('A message sating that the current phar is up to date');

        $I->runShellCommand("php $currentPharPath self-update");

        $I->dontSeeInShellOutput('Could not open input file:');
        $I->dontSeeInShellOutput('Error');
        $I->dontSeeInShellOutput('Exception');
        $I->dontSeeInShellOutput('Fatal');

        $I->seeInShellOutput('Looking for updates...');
        $I->seeInShellOutput('Already up-to-date.');


        // After
        $this->removeTestPhars();
    }


    /*
    |--------------------------------------------------------------------------
    | Private methods
    |--------------------------------------------------------------------------
    |
    |
    |
    */
    private function getPharVersion($phar)
    {
        exec("php $phar --version --no-ansi", $output);

        $version = !empty($output[0]) ? $output[0] : '';
        if (empty($version)) {
            return null;
        }

        return str_replace('TEQUILARAPIDO APPCLI version ', '', $version);
    }

    private function createTestPhars()
    {
        $fs = new Filesystem;

        $fs->copy(
            $this->getReleasesPath() . 'appcli-previousversion.phar',
            $this->getTestsPath() . $this->previousPhar,
            true
        );

        $fs->copy(
            $this->getReleasesPath() . 'appcli.phar',
            $this->getTestsPath() . $this->currentPhar,
            true
        );
    }

    private function removeTestPhars()
    {
        $fs = new Filesystem;

        $fs->remove($this->getTestsPath() . $this->previousPhar);
        $fs->remove($this->getTestsPath() . $this->currentPhar);
    }

    private function  getTestsPath()
    {
        return TEQ_PROJECT_ROOT . '/dist/tests/';
    }

    private function getReleasesPath()
    {
        return TEQ_PROJECT_ROOT . '/dist/downloads/';
    }


}