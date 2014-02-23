<?php
namespace Codeception\Module;

use Guzzle\Http\Client;

class TestHelper extends \Codeception\Module
{

    const LAUNCHER_CONSOLE = 'CONSOLE';
    const LAUNCHER_PHAR = 'PHAR';

    static $consoleCommand = 'php console.php';
    static $pharCommand = 'php dist/downloads/appcli.phar';

    /**
     * @var Client
     */
    private $mailcatcher;
    private $mail;

    public function _before()
    {
        // Create mailcatcher client
        $this->mailcatcher = new Client('http://127.0.0.1:1080');

        // Clean message before each test
        $this->cleanMessages();

        // Witch launcher ?
        $this->debug('Running with LANCHER=' . $this->getLauncher());
    }

    /*
    |--------------------------------------------------------------------------
    | Mail testing helper
    |--------------------------------------------------------------------------
    |
    |
    |
    */

    public function getEmail()
    {
        return $this->mail;
    }

    public function cleanMessages()
    {
        $this->mailcatcher->delete('/messages')->send();
    }

    public function getLastMessage()
    {
        $messages = $this->getMessages();
        if (empty($messages)) {
            $this->fail("No messages received");
        }

        // messages are in descending order
        $this->mail = reset($messages);
        return $this->mail;
    }

    public function getMessages()
    {
        $jsonResponse = $this->mailcatcher->get('/messages')->send();
        return json_decode($jsonResponse->getBody());
    }

    public function seeThatEmailIsSent($description = '')
    {
        $this->assertNotEmpty($this->getMessages(), $description);
    }

    public function seeThatEmailSubjectContains($needle, $description = '')
    {
        $this->assertContains($needle, $this->mail->subject, $description);
    }

    public function seeThatEmailSubjectEquals($expected, $description = '')
    {
        $this->assertEquals($expected, $this->mail->subject, $description);
    }

    public function seeThatEmailHtmlContains($needle, $description = '')
    {
        $response = $this->mailcatcher->get("/messages/{$this->mail->id}.html")->send();
        $this->assertContains($needle, (string)$response->getBody(), $description);
    }

    public function seeThatEmailTextContains($needle, $description = '')
    {
        $response = $this->mailcatcher->get("/messages/{$this->mail->id}.plain")->send();
        $this->assertContains($needle, (string)$response->getBody(), $description);
    }

    public function seeThatEmailSenderEquals($address, $description = '')
    {
        $response = $this->mailcatcher->get("/messages/{$this->mail->id}.json")->send();
        $email = json_decode($response->getBody());
        $this->assertEquals($this->wrap($address), $email->sender, $description);
    }

    public function seeThatEmailIsInRecipients($address, $description = '')
    {
        $response = $this->mailcatcher->get("/messages/{$this->mail->id}.json")->send();
        $email = json_decode($response->getBody());
        $this->assertTrue(in_array($this->wrap($address), $email->recipients), $description);
    }


    private function wrap($address)
    {
        return '<' . $address . '>';
    }

    /*
    |--------------------------------------------------------------------------
    | Cli helpers
    |--------------------------------------------------------------------------
    |
    |
    |
    */

    protected function getLauncher()
    {
        if (getenv('LAUNCHER') == static::LAUNCHER_PHAR) {
            return static::LAUNCHER_PHAR;
        }

        return static::LAUNCHER_CONSOLE;
    }

    protected function getPharCommand($command)
    {
        return static::$pharCommand . ' ' . $command;
    }

    protected function getConsoleCommand($command)
    {
        return static::$consoleCommand . ' ' . $command;
    }

    /**
     * Run commands
     *
     * @param $command
     */
    public function run($command)
    {
        if ($this->getLauncher() === static::LAUNCHER_PHAR) {
            $this->runUsingPhar($command);
        } else {
            $this->runUsingConsole($command);
        }
    }

    public function runUsingPhar($command)
    {
        $command = $this->getPharCommand($command);
        $cli = $this->getModule('Cli');
        $cli->runShellCommand($command);
    }

    public function runUsingConsole($command)
    {
        echo 'runUsingConsole';
        $command = $this->getConsoleCommand($command);
        $cli = $this->getModule('Cli');
        $cli->runShellCommand($command);
    }

}
