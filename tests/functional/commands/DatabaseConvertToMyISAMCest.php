<?php namespace commands;

use TestGuy;

class DatabaseConvertToMyISAMCest
{

    /**
     * @param TestGuy $I
     */
    public function convert_all_database_tables_to_myisam(TestGuy $I)
    {
        $I->wantTo('to run db:myisam to convert all wp_v381 tables to myisam');
        $I->run('db:myisam tests/_data/fixtures/configuration/wp-381.json');

        $I->dontSeeInShellOutput('Error');
        $I->seeInShellOutput('100%');
        $I->seeInShellOutput('Done. All tables are now MyISAM');
    }
}