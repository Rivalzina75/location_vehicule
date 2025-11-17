<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Lang;

class CustomResetPasswordNotification extends Notification
{
    use Queueable;

    /**
     * Le jeton de réinitialisation de mot de passe.
     *
     * @var string
     */
    public $token;

    /**
     * Créez une nouvelle instance de notification.
     */
    public function __construct($token)
    {
        $this->token = $token;
    }

    /**
     * Obtenez les canaux de notification.
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Obtenez la représentation par e-mail de la notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        // Construit l'URL de réinitialisation (ex: http://.../reset-password/TOKEN)
        $resetUrl = url(route('password.reset', [
            'token' => $this->token,
            'email' => $notifiable->getEmailForPasswordReset(),
        ], false));

        // Construit l'e-mail en utilisant nos clés de traduction JSON
        return (new MailMessage)
            ->subject(Lang::get('Réinitialisation de votre mot de passe'))
            ->line(Lang::get('Vous recevez cet e-mail car nous avons reçu une demande de réinitialisation de mot de passe pour votre compte.'))
            ->action(Lang::get('Réinitialiser le mot de passe'), $resetUrl)
            ->line(Lang::get('Ce lien de réinitialisation expirera dans :count minutes.', ['count' => config('auth.passwords.' . config('auth.defaults.passwords') . '.expire')]))
            ->line(Lang::get('Si vous n\'avez pas demandé de réinitialisation, aucune autre action n\'est requise.'));
    }

    /**
     * Obtenez la représentation en tableau de la notification.
     */
    public function toArray(object $notifiable): array
    {
        return [
            //
        ];
    }
}
