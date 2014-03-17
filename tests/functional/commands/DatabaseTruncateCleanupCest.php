<?php namespace commands;

use \TestGuy;

class DatabaseTruncateCleanupCest
{

    /**
     * @param TestGuy $I
     */
    public function run_command_with_empty_truncate_configuration(TestGuy $I)
    {
        $I->wantTo('run db:truncate command with no truncate configuration');
        $I->expect('commands will run, and warns us about empty config.');
        $I->run('db:truncate tests/_data/fixtures/configuration/wp-381.json');

        $I->dontSeeInShellOutput('Error');
        $I->seeInShellOutput('[WARN] There is nothing to truncate according to configuration.');
    }

    /**
     * @param TestGuy $I
     */
    public function run_command_with_simple_and_multiple_truncate(TestGuy $I)
    {
        $I->wantTo('run db:truncate command with simple and multiple truncate');
        $I->run('db:truncate tests/_data/fixtures/configuration/wp-381-with-truncate-operations.json');

        $I->dontSeeInShellOutput('Error');

        $I->seeInShellOutput('Truncating wp_comments ...');
        $I->dontSeeInDatabase('wp_comments');

        $I->seeInShellOutput('Truncating wp_posts ...');
        $I->dontSeeInDatabase('wp_posts');

        $I->seeInShellOutput('Truncating wp_1000_comments ...');
        $I->dontSeeInDatabase('wp_1000_comments');

        $I->seeInShellOutput('Truncating wp_1000_posts ...');
        $I->dontSeeInDatabase('wp_1000_posts');

        $I->seeInShellOutput('Database size: before');
    }

}