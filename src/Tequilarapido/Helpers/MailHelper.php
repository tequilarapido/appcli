<?php namespace Tequilarapido\Helpers;

use Carbon\Carbon;
use Swift_MailTransport;
use Swift_Mailer;
use Swift_Message;
use Tequilarapido\Cli\Config\Config;

class MailHelper
{
    protected $confi;

    public function __construct(Config $config)
    {
        $this->config = $config;
    }

    /**
     * @param string $commandName
     */
    public function send($mail, $commandName)
    {
        // to & from
        $from = $this->config->getNotificationFrom();
        $to = $this->config->getNotificationTo();

        // Mail content
        $givenSubject = isset($mail['subject']) ? $mail['subject'] : '';
        $subject = '[' . $this->config->getProject() . '][' . $commandName . ']' . ' : ' . $givenSubject;

        $givenBody = isset($mail['body']) ? $mail['body'] : '';
        $body = array();
        $body[] = $givenBody;
        $body[] = 'Command run at : ' . Carbon::now()->format('Y-m-d H:i:s');
        $body = implode("\n", $body);

        // Transport
        $transport = $this->getMailTransportFromConfiguration();

        // Send
        $this->sendMessageUsingTransport($transport, $from, $to, $subject, $body);
    }

    private function getMailTransportFromConfiguration()
    {
        $transport = null;
        $notificationTransport = $this->config->getNotificationTransport();

        // Try SMTP if it correctly configured
        if (
        !is_null($notificationTransport) &&
        !empty($notificationTransport->type) &&
        $notificationTransport->type === 'smtp' &&
        !empty($notificationTransport->parameters->host) &&
        !empty($notificationTransport->parameters->port)
    ) {
            $transport = \Swift_SmtpTransport::newInstance($notificationTransport->parameters->host, $notificationTransport->parameters->port);

            // Username ?
            if (!empty($notificationTransport->parameters->username)) {
                $transport->setUsername($notificationTransport->parameters->username);
            }

            // Username ?
            if (!empty($notificationTransport->parameters->password)) {
                $transport->setPassword($notificationTransport->parameters->password);
            }
        }

        // Otherwise just try default server transport
        if (is_null($transport)) {
            $transport = Swift_MailTransport::newInstance();
        }

        return $transport;
    }

    /**
     * @param string $subject
     * @param string $body
     */
    private function sendMessageUsingTransport($transport, $from, $to, $subject, $body)
    {
        $message = Swift_Message::newInstance();
        $message->setFrom($from);
        $message->setTo($to);
        $message->setSubject($subject);
        $message->setBody($body);

        $mailer = Swift_Mailer::newInstance($transport);
        $mailer->send($message);
    }

}