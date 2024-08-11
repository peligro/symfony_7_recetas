<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class RutasController extends AbstractController
{
    #[Route('/rutas', name: 'rutas_get', methods:['get'])]
    public function index(): JsonResponse
    {
        return $this->json([
             'estado'=>'ok',
            'mensaje'=>"mensaje desde GET"
        ]);
    }

    #[Route('/rutas/{id}', methods:['get'])]
    public function show(int $id): JsonResponse
    {
        return $this->json([
             'estado'=>'ok',
            'mensaje'=>"mensaje desde GET con parámetro =".$id
        ]);
    }

    
    #[Route('/rutas', methods:['post'])]
    public function create(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        return $this->json([
            'estado'=>'ok',
            'mensaje'=>"mensaje desde POST",
            'data'=>"correo=".$data['correo']." | password=".$data['password']
        ]);
    }
    #[Route('/rutas/{id}', methods:['put'])]
    public function update(int $id): JsonResponse
    {
        return $this->json([
             'estado'=>'ok',
            'mensaje'=>"mensaje desde PUT con parámetro =".$id
        ]);
    }
    #[Route('/rutas/{id}', methods:['delete'])]
    public function delete(int $id): JsonResponse
    {
        return $this->json([
             'estado'=>'ok',
            'mensaje'=>"mensaje desde DELETE con parámetro =".$id
        ]);
    }
    ####adicionales
    #[Route('/rutas-querystring', methods:['get'])]
    public function metodo_get_query_string(Request $request ): JsonResponse
    {
        return $this->json([
            'estado' => 'ok',
            'mensaje' => 'método GET | id = '.$request->query->get('id')." | id=".$_GET['slug']." " ,
        ]);
       
    }
    #[Route('/rutas-download', methods:['get'])]
    public function download(): BinaryFileResponse
    {
        //https://symfony.com/doc/current/components/mime.html
        //composer require symfony/mime
        return $this->file('img/foto.jpg');
    }
}
