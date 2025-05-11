<?php

namespace App\Notifications;

use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Notifications\Messages\MailMessage;

class ResetPasswordNotification extends ResetPassword
{
    public function toMail($notifiable)
    {
        $frontendUrl = config('app.frontend_url');
        $url = "{$frontendUrl}/reset-password?token={$this->token}&email=" . urlencode($notifiable->getEmailForPasswordReset());

        return (new MailMessage)
            ->subject('🎬 Resetowanie hasła – CinemaManager')
            ->greeting('Cześć kinomaniaku!')
            ->line('Otrzymaliśmy prośbę o zresetowanie hasła do Twojego konta CinemaManager.')
            ->line('Jeśli to Ty zainicjowałeś tę operację, kliknij poniższy przycisk, aby ustawić nowe hasło.')
            ->action('Zresetuj hasło', $url)
            ->line('Link do zresetowania hasła ze względów bezpieczeństwa będzie aktywny przez 60 minut.')
            ->line('Jeśli nie prosiłeś o reset hasła, po prostu zignoruj tę wiadomość – Twoje konto pozostanie bez zmian.')
            ->salutation('🎟️ Zespół CinemaManager');
    }
}
