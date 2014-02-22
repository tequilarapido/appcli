<?php namespace commands;

use \TestGuy;

class DatabaseDeleteCleanupCest
{

    public function run_command_with_empty_delete_configuration(TestGuy $I)
    {
        $I->wantTo('Run db:delete command with no delete configuration');
        $I->expect('commands will run, and warns us about empty config.');
        $I->run('db:delete tests/_data/fixtures/configuration/wp-381.json');

        $I->dontSeeInShellOutput('Error');
        $I->seeInShellOutput('[WARN] There is nothing to delete according to configuration.');
    }


    public function run_command_with_multiple_delete_operations(TestGuy $I)
    {
        $I->wantTo('Run db:delete command with multiple delete operations');
        $I->run('db:delete tests/_data/fixtures/configuration/wp-381-with-delete-operations.json');

        $I->dontSeeInShellOutput('Error');

        $I->seeInShellOutput('Deleting items from table wp_1000_posts ...');
        $I->dontSeeInDatabase('wp_1000_posts');

        $I->seeInShellOutput('Deleting items from table wp_posts ...');
        $I->dontSeeInDatabase('wp_posts');

        $I->seeInShellOutput('Deleting items from table wp_comments ...');
        $I->dontSeeInDatabase('wp_comments');

        $I->seeInShellOutput('Database size: before');
    }
}