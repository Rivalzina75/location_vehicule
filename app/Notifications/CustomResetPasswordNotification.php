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
        $resetUrl = url(route('password.reset', [
            'token' => $this->token,
            'email' => $notifiable->getEmailForPasswordReset(),
        ], false));

        // Récupération du délai d'expiration (60 min par défaut)
        $expirationCount = config('auth.passwords.' . config('auth.defaults.passwords') . '.expire');

        // Utilisation de la vue multilingue
        return (new MailMessage)
            ->subject(Lang::get('Réinitialisation de votre mot de passe') . ' - Machina')
            ->view('emails.password-reset', [
                'url' => $resetUrl,
                'count' => $expirationCount,
                'user' => $notifiable
            ]);
    }
}
