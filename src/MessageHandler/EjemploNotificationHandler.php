<?php
namespace App\MessageHandler;

use App\Message\EjemploNotificacion;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use App\Entity\Contacto;
use Doctrine\ORM\EntityManagerInterface; 

#[AsMessageHandler]
class EjemploNotificationHandler
{
    private $em;
   

    public function __construct(EntityManagerInterface $em   )
    {
        $this->em=$em; 
    }
    public function __invoke(EjemploNotificacion $message)
    {
        ;
        $entity = new Contacto();
        $entity->setNombre($message->content->nombre);
        $entity->setTelefono($message->content->telefono);
        $entity->setCorreo($message->content->correo);
        $entity->setMensaje($message->content->mensaje);
        $entity->setFecha(new \DateTime()); 
        $this->em->persist($entity); 
        $this->em->flush();
    }
}