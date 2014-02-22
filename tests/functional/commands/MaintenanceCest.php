<?php namespace commands;

use \TestGuy;

class MaintenanceCest
{

    public function put_maintenance_off(TestGuy $I)
    {
        $I->wantTo('Put maintenance to "off"');
        $I->run('maintenance off --dont-check-directory');

        $I->dontSeeInShellOutput('Error');
        $I->seeInShellOutput('Maintenance mode is now off');
    }

    public function put_maintenance_on(TestGuy $I)
    {
        $I->wantTo('Put maintenance to "on"');
        $I->run('maintenance on --dont-check-directory');

        $I->dontSeeInShellOutput('Error');
        $I->seeInShellOutput('Maintenance mode is now on');
        $I->seeFileFound('.maintenance');
    }

    public function get_maintenance_status_when_it_is_off(TestGuy $I)
    {
        // Status : Off
        $I->wantTo('Get the maintenance status when it is off');
        $I->run('maintenance off --dont-check-directory');
        $I->run('maintenance status --dont-check-directory');
        $I->dontSeeInShellOutput('Error');
        $I->seeInShellOutput('Current Maintenance mode : off');
    }

    public function get_maintenance_status_when_it_is_on(TestGuy $I)
    {
        // Status : On
        $I->wantTo('Get the maintenance status when it is on');
        $I->run('maintenance on --dont-check-directory');
        $I->run('maintenance status --dont-check-directory');
        $I->dontSeeInShellOutput('Error');
        $I->seeInShellOutput('Current Maintenance mode : on');
    }


}