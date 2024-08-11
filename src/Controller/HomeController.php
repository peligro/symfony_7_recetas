<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;

class HomeController extends AbstractController
{
    #[Route('/', name: 'home_index')]
    public function index(): JsonResponse
    {
        return $this->json([
            'mensaje' => 'API Rest con Symofny 7.1',
        ]);
    }
}
