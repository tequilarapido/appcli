<?php namespace commands;

use \TestGuy;

class MaintenanceCest
{
    /**
     * @param TestGuy $I
     */
    public function put_maintenance_off(TestGuy $I)
    {
        $I->wantTo('run maintenance command to tput maintenance to "off"');
        $I->run('maintenance off --dont-check-directory');

        $I->dontSeeInShellOutput('Error');
        $I->seeInShellOutput('Maintenance mode is now off');
    }


    /**
     * @param TestGuy $I
     * @env phar
     * @env console
     */
    public function put_maintenance_on(TestGuy $I)
    {
        $I->wantTo('run maintenance command to put maintenance to "on"');
        $I->run('maintenance on --dont-check-directory');

        $I->dontSeeInShellOutput('Error');
        $I->seeInShellOutput('Maintenance mode is now on');
        $I->seeFileFound('.maintenance');
    }

    /**
     * @param TestGuy $I
     * @env phar
     * @env console
     */
    public function get_maintenance_status_when_it_is_off(TestGuy $I)
    {
        // Status : Off
        $I->wantTo('run maintenance command to get the maintenance status when it is off');
        $I->run('maintenance off --dont-check-directory');
        $I->run('maintenance status --dont-check-directory');
        $I->dontSeeInShellOutput('Error');
        $I->seeInShellOutput('Current Maintenance mode : off');
    }

    /**
     * @param TestGuy $I
     * @env phar
     * @env console
     */
    public function get_maintenance_status_when_it_is_on(TestGuy $I)
    {
        // Status : On
        $I->wantTo('run maintenance command to get the maintenance status when it is on');
        $I->run('maintenance on --dont-check-directory');
        $I->run('maintenance status --dont-check-directory');
        $I->dontSeeInShellOutput('Error');
        $I->seeInShellOutput('Current Maintenance mode : on');
    }


}