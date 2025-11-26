<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Lang;

class CustomResetPasswordNotification extends Notification
{
    use Queueable;

    public $token;

    public function __construct($token)
    {
        $this->token = $token;
    }

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        // 1. Génération du lien de reset
        $resetUrl = url(route('password.reset', [
            'token' => $this->token,
            'email' => $notifiable->getEmailForPasswordReset(),
        ], false));

        // 2. Récupération du délai d'expiration (60 min par défaut)
        $expirationCount = config('auth.passwords.' . config('auth.defaults.passwords') . '.expire');

        // 3. On renvoie la VUE personnalisée
        return (new MailMessage)
            ->subject(Lang::get('Réinitialisation de mot de passe - Machina'))
            ->view('emails.password-reset', [
                'url' => $resetUrl,
                'count' => $expirationCount,
                'user' => $notifiable
            ]);
    }
}
