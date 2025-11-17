<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\URL; // Important pour générer l'URL
use Illuminate\Support\Facades\Lang; // Important pour la traduction

class VerifyEmailFrench extends Notification
{
    use Queueable;

    /**
     * Créez une nouvelle instance de notification.
     */
    public function __construct()
    {
        //
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
        // On génère l'URL de vérification sécurisée
        $verificationUrl = $this->verificationUrl($notifiable);

        // On construit l'e-mail en utilisant nos clés JSON
        return (new MailMessage)
            ->subject(Lang::get('Vérifiez votre adresse e-mail'))
            ->line(Lang::get('Avant de continuer, veuillez vérifier votre boîte de réception pour un lien de vérification.'))
            ->action(Lang::get('Cliquez ici pour en recevoir un autre'), $verificationUrl)
            ->line(Lang::get('Si vous n\'avez pas reçu l\'e-mail'));
    }

    /**
     * Génère l'URL de vérification.
     */
    protected function verificationUrl(object $notifiable): string
    {
        return URL::temporarySignedRoute(
            'verification.verify',
            now()->addMinutes(config('auth.verification.expire', 60)),
            [
                'id' => $notifiable->getKey(),
                'hash' => sha1($notifiable->getEmailForVerification()),
            ]
        );
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
