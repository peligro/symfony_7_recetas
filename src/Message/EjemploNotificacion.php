<?php 
namespace App\Message;
use Symfony\Component\Mailer\MailerInterface;

class EjemploNotificacion
{
    public function __construct(
        public $content,
    ) {
    }

    public function getContent(): array
    {
        
        return $this->content;
    }
     
}