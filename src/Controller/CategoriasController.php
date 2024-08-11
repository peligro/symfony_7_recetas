<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;
use Doctrine\ORM\EntityManagerInterface; 
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\String\Slugger\SluggerInterface; 
use App\Entity\Categoria;
use App\Entity\Receta;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use App\Dto\CategoriaDto;  

class CategoriasController extends AbstractController
{
    private $em;
    //composer require symfony/string
    //composer require symfony/translation
    private $slugger;

    public function __construct(EntityManagerInterface $em , SluggerInterface $slugger )
    {
        $this->em=$em;
        $this->slugger=$slugger;
    }
    #[Route('/categorias', methods: ['get'])]
    public function index(): JsonResponse
    {
        $datos= $this->em->getRepository(Categoria::class)->findBy(array(), array('id'=>'desc'));
        foreach($datos as $dato)
        {
            $data[]=['id'=>$dato->getId(),'nombre'=> $dato->getNombre(), 'slug'=>$dato->getSlug()];
        } 
        return $this->json($data, $status = Response::HTTP_OK);
    }
    #[Route('/categorias/{id}', methods: ['get'])]
    public function show(int $id): JsonResponse
    {
        $datos= $this->em->getRepository(Categoria::class)->find($id);
        if(!$datos)
        {
            
            return $this->json([
                'estado' => 'error',
                'mensaje'=>'La URL no está disponible en este momento'
                
            ], Response::HTTP_NOT_FOUND); 
            
        }
        return $this->json(['id'=>$datos->getId(),'nombre'=> $datos->getNombre(), 'slug'=>$datos->getSlug()]);
    }
    #[Route('/api/categorias', methods: ['post'])]
    public function create(Request $request, #[MapRequestPayload] CategoriaDto $dto  ): JsonResponse
    { 
        $entity = new Categoria();
        $entity->setNombre($dto->nombre);
        $entity->setSlug( $this->slugger->slug(strtolower($dto->nombre)));  
        $this->em->persist($entity); 
        $this->em->flush();
        return $this->json([
            'estado' => 'ok',
            'mensaje'=>'Se creó el registro exitosamente'
            
        ], Response::HTTP_CREATED); 
    }
    #[Route('/api/categorias/{id}', methods: ['put'])]
    public function update(Request $request, #[MapRequestPayload] CategoriaDto $dto, int $id ): JsonResponse
    {
        $datos= $this->em->getRepository(Categoria::class)->find($id);
        if(!$datos)
        {
            
            return $this->json([
                'estado' => 'error',
                'mensaje'=>'La URL no está disponible en este momento'
                
            ], RESPONSE::HTTP_NOT_FOUND);  
        }
         
        $datos->setNombre($dto->nombre);
        $datos->setSlug( $this->slugger->slug(strtolower($dto->nombre)));  
        //$this->em->persist($entity); 
        $this->em->flush();
        return $this->json([
            'estado' => 'ok',
            'mensaje'=>'Se modificó el registro exitosamente'
            
        ], RESPONSE::HTTP_OK); 
    }
    #[Route('/api/categorias/{id}', methods: ['delete'])]
    public function destroy(Request $request, int $id ): JsonResponse
    {
        $datos= $this->em->getRepository(Categoria::class)->find($id);
        if(!$datos)
        {
            
            return $this->json([
                'estado' => 'error',
                'mensaje'=>'La URL no está disponible en este momento'
                
            ], RESPONSE::HTTP_NOT_FOUND);  
        } 
        $existe =  $this->em->getRepository(Receta::class)->findBy( array('categoria'=>$id ), 
        array()) ;
        if($existe)
        {
            return new JsonResponse([
                'estado'=>'error',
                'mensaje' => 'No se pudo completar la petición, ocurrió un error inesperado'
            ], RESPONSE::HTTP_BAD_REQUEST);
        }else
        {
            $this->em->remove($datos);
            $this->em->flush();
            return $this->json([
                'estado' => 'ok',
                'mensaje'=>'Se eliminó el registro exitosamente'
            ], RESPONSE::HTTP_OK);
        } 
    }
}
