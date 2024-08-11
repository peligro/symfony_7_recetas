<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Usuarios;
use App\Entity\Estado;

class VerificacionController extends AbstractController
{
    private $em;
    public function __construct(EntityManagerInterface $em)
    {
        $this->em=$em; 
    }
    #[Route('/auth/verificacion/{token}', methods:['get'])]
    public function show($token): Response
    {
        $user = $this->em->getRepository(Usuarios::class)->findOneBy(['token' => $token, 'estado'=>$this->em->getRepository(Estado::class)->find(2)]);
        if(!$user)
        {
            return $this->json([
                'estado' => 'error', 
                'password'=>"Recurso no disponible" 
    
            ], Response::HTTP_NOT_FOUND);
        }
        $user->setToken('');
        $user->setEstado( $this->em->getRepository(Estado::class)->find(1));  
        //$this->em->persist($entity); 
        $this->em->flush();
        /*return $this->json([
            'estado' => 'ok', 
            'password'=>"Acción realizada" 

        ], Response::HTTP_OK);*/
        return $this->redirect('http://192.168.1.88:4200/login');
    }
}
