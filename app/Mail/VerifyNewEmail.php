<?php
namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class VerifyNewEmail extends Mailable
{
    use Queueable, SerializesModels;

    public $user;

    public function __construct(User $user)
    {
        $this->user = $user;
    }

    public function build()
    {
        return $this->subject('Potwierdź nowy adres e-mail')
            ->view('emails.verify-new-email')
            ->with([
                'name' => $this->user->name,
                'url' => url("/api/verify-email/{$this->user->id}/{$this->user->email_verification_token}"),
            ]);
    }

}
