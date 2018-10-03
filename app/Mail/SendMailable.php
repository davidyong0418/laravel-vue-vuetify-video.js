<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class SendMailable extends Mailable
{
    use Queueable, SerializesModels;
    public $name;
    public $new_password;
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($name, $new_password)
    {
        $this->name = $name;
        $this->password = $new_password;

    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->view('mail',['name' =>$this->name, 'password' => $this->password]);
    }
}