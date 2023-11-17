<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class KartuMembership extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public $name, $verified, $url;
    public function __construct($name, $verified, $url = null)
    {
        $this->name = $name;
        $this->verified = $verified;
        $this->url = $url;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->view('emails.card')
            ->subject($this->verified ? 'Membership Diaktifkan' : 'Membership Dinonaktifkan');
    }
}
