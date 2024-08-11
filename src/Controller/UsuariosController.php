<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Doctrine\ORM\EntityManagerInterface;
use App\Dto\RegistroDto;
use App\Dto\LoginDto;
use App\Entity\Usuarios;
use App\Entity\Estado;

use Symfony\Component\Mime\Email;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;

//para generar el token
use Firebase\JWT\JWT;//composer require firebase/php-jwt
use Firebase\JWT\Key;
class UsuariosController extends AbstractController
{
    private $em;
    public function __construct(EntityManagerInterface $em)
    {
        $this->em=$em; 
    }
    #[Route('/auth/login', methods:['POST'])]
    public function login(Request $request, UserPasswordHasherInterface $passwordHasher, #[MapRequestPayload] LoginDto $dto): JsonResponse
    {
        $user = $this->em->getRepository(Usuarios::class)->findOneBy(['correo' => $dto->correo, 'estado'=>$this->em->getRepository(Estado::class)->find(1)]);
        if(!$user)
        {
            return $this->json([
                'estado' => 'error', 
                'password'=>"Las credenciales ingresadas no son válidas" 
    
            ], Response::HTTP_BAD_REQUEST);
        }
        if ($passwordHasher->isPasswordValid($user, $dto->password)) 
        {
            $fecha = date_create(date('Y-m-d'));
            $timestamp=date_add($fecha, date_interval_create_from_date_string('1 days'));
            $payload = [
                'iss'=>$request->getUriForPath(""),
                'aud'=>$user->getId(),
                'iat'=>time(),
                'exp' => strtotime($timestamp->format('Y-m-d'))
            ];
            
            $jwt = JWT::encode($payload, $_ENV['JWT_SECRET'], 'HS512');
            //$jwt="ss";
            return $this->json(
                [
                    'id'=>$user->getId(),
                    'nombre'=>$user->getNombre(),
                    'token'=>$jwt
                    
                ], Response::HTTP_OK);
        }else
        {
            return $this->json([
                'estado' => 'error', 
                'password'=>"Las credenciales ingresadas no son válidas" 
    
            ], Response::HTTP_BAD_REQUEST); 
        }
    }
    #[Route('/auth/registro', methods:['POST'])]
    public function create(Request $request, UserPasswordHasherInterface $passwordHasher, #[MapRequestPayload] RegistroDto $dto, MailerInterface $mailer): JsonResponse
    {
        $existe = $this->em->getRepository(Usuarios::class)->findOneBy(['correo' => $dto->correo]);
        if($existe)
        {
            return $this->json([
                'estado' => 'error',
                'mensaje'=>"El correo {$dto->correo} ya está siendo usado por otro usuario"
                
            ], Response::HTTP_BAD_REQUEST);
        }
        $token=sha1(uniqid().rand(1, 100000).time()) ;
        $entity = new Usuarios();
        $entity->setNombre($dto->nombre);
        $entity->setCorreo($dto->correo);
        $entity->setPassword($passwordHasher->hashPassword($entity, $dto->password)); 
        $entity->setRoles(['ROLE_USER']);
        $entity->setEstado($this->em->getRepository(Estado::class)->find(2));
        $entity->setToken($token);
        $this->em->persist($entity); 
        $this->em->flush();


        $url=$request->getUriForPath("/auth/verificacion/".$token);
        $email = (new Email())
        ->from(new Address('test@test.com', 'Curso fullstack'))
        ->to($dto->correo)

        ->subject('Curso fullstack')
        //->text('Texto del mail')
        ->html('<h1>Verificación de contraseña</h1>
                Hola '.$dto->nombre.' te haz registrado exitosamente, para activar tu cuenta por favor haz click aquí <a href="'.$url.'">'.$url.'</a> 
                <br/>
                o copia y pega la siguiente URL en tu navegador favorito
                <br/>
                '.$url.' ')    
        ;
        try {
            $mailer->send($email);
            return $this->json([
                'estado' => 'ok', 
                'mensaje'=>"Se creó el registro exitosamente" 
    
            ], Response::HTTP_CREATED);
        } catch (TransportExceptionInterface $e) {
            return $this->json([
                'estado' => 'error',
                'mensaje' => 'Ups ocurrió un error inesperado '.$e,
            ], Response::HTTP_BAD_REQUEST);
        }
        
    }
    /*
    #[Route('/auth/registro', methods:['POST'])]
    public function create(Request $request, UserPasswordHasherInterface $passwordHasher, #[MapRequestPayload] RegistroDto $dto): JsonResponse
    {
        $existe = $this->em->getRepository(Usuarios::class)->findOneBy(['correo' => $dto->correo]);
        if($existe)
        {
            return $this->json([
                'estado' => 'error',
                'mensaje'=>"El correo {$dto->correo} ya está siendo usado por otro usuario"
                
            ], Response::HTTP_BAD_REQUEST);
        }
        $entity = new Usuarios();#p2gHNiENUw
        $entity->setNombre($dto->nombre);
        $entity->setCorreo($dto->correo);
        $entity->setPassword($passwordHasher->hashPassword($entity, $dto->password)); 
        $entity->setRoles(['ROLE_USER']);
        $this->em->persist($entity); 
        $this->em->flush();
        return $this->json([
            'estado' => 'ok', 
            'mensaje'=>"Se creó el registro exitosamente" 

        ], Response::HTTP_CREATED);
    }*/
}
