<?php namespace commands;

use \TestGuy;

class DatabaseOccurrencesCest
{

    /**
     * @param TestGuy $I
     */
    public function run_command_with_empty_replace_configuration(TestGuy $I)
    {
        $I->wantTo('Run db:occurrences command with no replace configuration');
        $I->expect('commands will run, and warns us about empty config.');
        $I->run('db:occurrences --no-ansi tests/_data/fixtures/configuration/wp-381.json');

        $I->dontSeeInShellOutput('Error');
        $I->seeInShellOutput('[WARN] There is nothing to search according to configuration.');
    }

    /**
     * @param TestGuy $I
     */
    public function run_command_with_search_based_on_config_configuration(TestGuy $I)
    {
        $I->wantTo('Run db:occurrences command with no replace configuration');
        $I->run('db:occurrences --no-ansi  tests/_data/fixtures/configuration/wp-381-with-replace-operations.json');

        $I->dontSeeInShellOutput('Error');
        $I->seeInShellOutput('Occurrences by table');
        $I->seeInShellOutput('wp_1000_posts : 3');
        $I->seeInShellOutput('wp_options : 5');
        $I->seeInShellOutput('wp_posts : 3');
        $I->seeInShellOutput('Total occurrences : 11');
    }

    /**
     * @param TestGuy $I
     */
    public function run_command_with_simple_search_from_command_argument(TestGuy $I)
    {
        $I->wantTo('Run db:occurrences command with no replace configuration');
        $I->run('db:occurrences --no-ansi  tests/_data/fixtures/configuration/wp-381.json "admin@"');

        $I->dontSeeInShellOutput('Error');
        $I->seeInShellOutput('Occurrences by table');
        $I->seeInShellOutput('wp_options : 1');
        $I->seeInShellOutput('wp_users : 1');
        $I->seeInShellOutput('Total occurrences : 2');
    }

    /**
     * @param TestGuy $I
     */
    public function run_command_with_multiple_search_from_command_argument(TestGuy $I)
    {
        $I->wantTo('Run db:occurrences command with no replace configuration');
        $I->run('db:occurrences --no-ansi  tests/_data/fixtures/configuration/wp-381.json "admin@|mystery|newest"');

        $I->dontSeeInShellOutput('Error');
        $I->seeInShellOutput('Occurrences by table');
        $I->seeInShellOutput('wp_options : 3');
        $I->seeInShellOutput('wp_users : 1');
        $I->seeInShellOutput('Total occurrences : 4');
    }

    /**
     * @param TestGuy $I
     */
    public function run_command_with_search_from_command_argument_with_no_results(TestGuy $I)
    {
        $I->wantTo('Run db:occurrences command with no replace configuration');
        $I->run('db:occurrences --no-ansi  tests/_data/fixtures/configuration/wp-381.json "something-that-will-never-ever-exists-123467890"');

        $I->dontSeeInShellOutput('Error');
        $I->seeInShellOutput('Occurrences by table');
        $I->seeInShellOutput(' None.');
        $I->seeInShellOutput('Total occurrences : 0');
    }
}