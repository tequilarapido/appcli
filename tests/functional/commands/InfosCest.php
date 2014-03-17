<?php namespace commands;

use \TestGuy;

class InfosCest
{

    /**
     * @param TestGuy $I
     */
    public function get_infos_for_acme_project(TestGuy $I)
    {
        $I->wantTo('run infos command to get information about acme project configuration');
        $I->run('infos tests/_data/fixtures/configuration/acme.json');

        $I->dontSeeInShellOutput('Error');
        $I->seeInShellOutput('Configuration File : tests/_data/fixtures/configuration/acme.json');
        $I->seeInShellOutput('"project":"ACME",');
    }
}