<?php namespace commands;

use \TestGuy;

class DatabaseConvertToInnoDBCest
{

    /**
     * @param TestGuy $I
     */
    public function convert_all_database_tables_to_innodb(TestGuy $I)
    {
        $I->wantTo('Convert all wp_v381 tables to innodb');
        $I->run('db:innodb tests/_data/fixtures/configuration/wp-381.json');

        $I->dontSeeInShellOutput('Error');
        $I->seeInShellOutput('100%');
        $I->seeInShellOutput('Done. All tables are now InnoDB');
    }
}