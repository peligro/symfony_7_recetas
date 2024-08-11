<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use App\Dto\ContactoDto;

//composer require symfony/mailer
use Symfony\Component\Mime\Email;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;

use Symfony\Component\Messenger\MessageBusInterface;
use App\Message\EjemploNotificacion;
 
class ContactoController extends AbstractController
{
     
   
    #[Route('/contacto', methods:['post'])]
    public function create(Request $request, #[MapRequestPayload] ContactoDto $dto, MailerInterface $mailer, MessageBusInterface $bus): JsonResponse
    {
        $email = (new Email())
        ->from(new Address('test@test.com', 'Curso fullstack'))
        ->to($dto->correo)

        ->subject('Curso fullstack')
        //->text('Texto del mail')
        ->html('<h1>Nuevo mensaje sitio web</h1>
                <ul>
                <li><strong>Nombre:</strong>'.$dto->nombre.'</li>
                <li><strong>Teléfono:</strong>'.$dto->telefono.'</li>
                <li><strong>E-Mail:</strong>'.$dto->correo.'</li>
                <li><strong>Mensaje:</strong>'.$dto->mensaje.'</li>
                </ul>')    
        ;
        try {
            $bus->dispatch(new EjemploNotificacion($dto));
            $mailer->send($email);
            return $this->json([
                'estado'=>'ok',
                'mensaje'=>"Se envió el mail exitosamente"
            ], Response::HTTP_OK);
        } catch (TransportExceptionInterface $e) {
            return $this->json([
                'estado' => 'error',
                'mensaje' => 'Ups ocurrió un error inesperado '.$e,
            ], Response::HTTP_BAD_REQUEST);
        }
        
    }
        
}
