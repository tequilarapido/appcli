<?php namespace commands;

use \TestGuy;

class DatabaseConvertToUtf8Cest
{

    public function convert_all_database_tables_to_utf8(TestGuy $I)
    {
        $I->wantTo('Convert all wp_v381 tables charset to utf8 and all tables collation to utf_general_ci');
        $I->run('db:utf8 tests/_data/fixtures/configuration/wp-381.json');

        $I->dontSeeInShellOutput('Error');
        $I->seeInShellOutput('100%');
        $I->seeInShellOutput('Done. All table are now utf8/utf8_general_ci');
    }

}