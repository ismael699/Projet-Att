<?php

namespace App\Service;

use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;

class MailService
{
    private $mailer;

    public function __construct(MailerInterface $mailer)
    {
        $this->mailer = $mailer;
    }

    public function sendPasswordResetEmail(string $to, string $resetUrl): void
    {
        $email = (new Email())
            ->from('no-reply@example.com')
            ->to($to)
            ->subject('Réinitialisation de votre mot de passe');
            // ->html('<p>Cliquez sur le lien suivant pour réinitialiser votre mot de passe :</p><a href="' . $resetUrl . '">Réinitialiser le mot de passe</a>');

        $this->mailer->send($email);
    }
}