<?php

namespace App\Controllers;

use Anddye\Mailer\Mailable;

class OrderMailable extends Mailable
{
    
    protected $user;
    
    public function __construct($user)
    {
        $this->user = $user;
    }
    
    public function build()
    {
        $this->setSubject('Welcome to the Team!');
        $this->setView('./welcome.html.twig', [
            'user' => $this->user
        ]);
        
        return $this;
    }
    
}