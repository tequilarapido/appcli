<?php namespace commands;

use \TestGuy;

class DatabaseReplaceCest
{
    /**
     * There are options expected values after replace operation,
     *  from: wordpress-381.dev
     *  to: www.wordpress381.com
     * with differents tests like PHP serialized object and array ...
     *
     * @var array
     */
    static $wpOptionsExpectedOptions = array(
        'siteurl' => 'http://www.wordpress-381.com',
        'A_TEST_PHP_OBJECT' => 'O:8:"stdClass":2:{s:4:"type";s:17:"This is an object";s:3:"url";s:45:"http://www.wordpress-381.com/uri/to/resource/";}',
        'A_TEST_PHP_ARRAY' => 'a:2:{s:4:"type";s:16:"This is an array";s:3:"url";s:45:"http://www.wordpress-381.com/uri/to/resource/";}',
        'A_TEST_JSON_OBJECT' => '{"type":"This is an object","url":"http://www.wordpress-381.com/uri/to/resource/"}',
    );


    /**
     * @param TestGuy $I
     */
    public function run_command_with_empty_replace_configuration(TestGuy $I)
    {
        $I->wantTo('run db:replace with empty replace configuration');
        $I->expect('commands will run, and warns us about empty config.');
        $I->run('db:replace tests/_data/fixtures/configuration/wp-381.json');

        $I->dontSeeInShellOutput('Error');
        $I->seeInShellOutput('[WARN] There is nothing to replace according to configuration.');
    }

    /**
     * @param TestGuy $I
     */
    public function run_command_with_replace_operations_and_notifications_disabled(TestGuy $I)
    {
        $I->wantTo('run db:replace with replace operations and notifications disabled');
        $I->run('db:replace tests/_data/fixtures/configuration/wp-381-with-replace-operations.json');

        $I->dontSeeInShellOutput('Error');

        $I->seeInShellOutput('Analysing database : looking for text columns ...');
        $I->seeInShellOutput('Total executed queries : 11');

        foreach (static::$wpOptionsExpectedOptions as $optionName => $expectedOptionValue) {
            $I->expect("That the option <$optionName> option has been replaced correctly");
            $I->seeInDatabase('wp_options', array('option_name' => $optionName, 'option_value' => $expectedOptionValue));
        }

        $I->seeInShellOutput('No notifications sent.');
    }

    /**
     * @param TestGuy $I
     */
    public function run_command_with_replace_operations_and_notifications_disabled_and_using_transactions(TestGuy $I)
    {
        $I->wantTo('run db:replace with replace operations, notification disabled and using transactions');
        $I->run('db:replace --use-transactions tests/_data/fixtures/configuration/wp-381-with-replace-operations.json');

        $I->dontSeeInShellOutput('Error');

        $I->seeInShellOutput('Analysing database : looking for text columns ...');
        $I->seeInShellOutput('Queries were executed using transactions.');
        $I->seeInShellOutput('Total executed queries : 11');

        foreach (static::$wpOptionsExpectedOptions as $optionName => $expectedOptionValue) {
            $I->expect("That the option <$optionName> option has been replaced correctly");
            $I->seeInDatabase('wp_options', array('option_name' => $optionName, 'option_value' => $expectedOptionValue));
        }

        $I->seeInShellOutput('No notifications sent.');
    }

    /**
     * @param TestGuy $I
     */
    public function run_command_with_replace_operations_and_notifications_enabled_and_no_notification_config(TestGuy $I)
    {
        $I->wantTo('run db:replace with empty replace operations, notification enabled but with no notification configuration');
        $I->run('db:replace tests/_data/fixtures/configuration/wp-381-with-replace-operations-with-notification-but-no-config.json');

        $I->dontSeeInShellOutput('Error');

        $I->seeInShellOutput('Analysing database : looking for text columns ...');
        $I->seeInShellOutput('Total executed queries : 11');

        foreach (static::$wpOptionsExpectedOptions as $optionName => $expectedOptionValue) {
            $I->expect("That the option <$optionName> option has been replaced correctly");
            $I->seeInDatabase('wp_options', array('option_name' => $optionName, 'option_value' => $expectedOptionValue));
        }

        $I->seeInShellOutput('Notify is on, but notify configuration are missing.');
    }


    /**
     * @param TestGuy $I
     */
    public function run_command_with_replace_operations_and_notifications_via_mailcatcher(TestGuy $I)
    {
        $I->wantTo('run db:replace with replce operations and notification via mailcatcher/smtp');
        $I->run('db:replace tests/_data/fixtures/configuration/wp-381-with-replace-operations-with-notification-mailcatcher.json');

        $I->dontSeeInShellOutput('Error');

        $I->seeInShellOutput('Analysing database : looking for text columns ...');
        $I->seeInShellOutput('Total executed queries : 11');

        foreach (static::$wpOptionsExpectedOptions as $optionName => $expectedOptionValue) {
            $I->expect("That the option <$optionName> option has been replaced correctly");
            $I->seeInDatabase('wp_options', array('option_name' => $optionName, 'option_value' => $expectedOptionValue));
        }

        // notification ?
        $I->seeInShellOutput('Notification sent.');
        $I->getLastMessage();
        $I->seeThatEmailIsSent();
        $I->seeThatEmailSenderEquals('notify@appcli-example.com');
        $I->seeThatEmailIsInRecipients('nbourguig@gmail.com');
        $I->seeThatEmailSubjectEquals('[Wordpress 3.8.1][db:replace] : Done.');
        $I->seeThatEmailTextContains('Command run at :');
        $I->seeThatEmailTextContains('Using file : ');
    }

    /**
     *
     * @env nosendmail
     * @param TestGuy $I
     */
    public function run_command_with_replace_operations_and_notifications_via_mailcatcher_with_sendmail(TestGuy $I)
    {
        $I->wantTo('run db:replace with replce operations and notification via mailcatcher/sendmail');
        $I->run('db:replace tests/_data/fixtures/configuration/wp-381-with-replace-operations-with-notification-mailcatcher-sendmail.json');

        $I->dontSeeInShellOutput('Error');

        $I->seeInShellOutput('Analysing database : looking for text columns ...');
        $I->seeInShellOutput('Total executed queries : 11');

        foreach (static::$wpOptionsExpectedOptions as $optionName => $expectedOptionValue) {
            $I->expect("That the option <$optionName> option has been replaced correctly");
            $I->seeInDatabase('wp_options', array('option_name' => $optionName, 'option_value' => $expectedOptionValue));
        }

        // notification ?
        $I->seeInShellOutput('Notification sent.');
        $I->getLastMessage();
        $I->seeThatEmailIsSent();
        $I->seeThatEmailSenderEquals('notify@appcli-example.com');
        $I->seeThatEmailIsInRecipients('nbourguig@gmail.com');
        $I->seeThatEmailSubjectEquals('[Wordpress 3.8.1][db:replace] : Done.');
        $I->seeThatEmailTextContains('Command run at :');
        $I->seeThatEmailTextContains('Using file : ');
    }
}