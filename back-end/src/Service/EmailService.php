<?php

namespace App\Service;

use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;

class EmailService
{
    private MailerInterface $mailer;

    public function __construct(MailerInterface $mailer)
    {
        $this->mailer = $mailer;
    }

    public function sendWelcomeEmail(string $toEmail, string $name)
    {
        $email = (new Email())
            ->from('garciaquinteroga@gmail.com')
            ->to($toEmail)
            ->subject('Bienvenido a la empresa')
            ->html($this->getWelcomeEmailTemplate($name));

        $this->mailer->send($email);
    }

    private function getWelcomeEmailTemplate(string $name): string
    {
        return "
            <h1>Hola, $name!</h1>
            <p>Bienvenido a la empresa. Estamos emocionados de que te unas a nuestro equipo.</p>
            <p>Saludos,<br>El equipo de la empresa</p>
        ";
    }
}