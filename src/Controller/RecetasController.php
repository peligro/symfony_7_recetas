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
use App\Entity\Usuarios;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use App\Dto\RecetaDto;
use Firebase\JWT\JWT; 
use Firebase\JWT\Key;

//paginación
use Knp\Component\Pager\PaginatorInterface;
class RecetasController extends AbstractController
{
    private $em;
    private $slugger;

    public function __construct(EntityManagerInterface $em , SluggerInterface $slugger )
    {
        $this->em=$em;
        $this->slugger=$slugger;
    }
    #[Route('/recetas', methods: ['get'])]
    public function index(Request $request, PaginatorInterface $paginator): JsonResponse
    {
        $datos= $this->em->getRepository(Receta::class)->findBy(array(), array('id'=>'desc'));
        $pagination = $paginator->paginate($datos, $request->query->getInt('page', 1), 6);
        $pagination->setParam('parametro', 'page');
        foreach($pagination as $dato)
        { 
            $data[]=
            [
                'cuantos'=>sizeof($datos),
                'id'=>$dato->getId(),
                'nombre'=> $dato->getNombre(), 
                'slug'=>$dato->getSlug(),
                'tiempo'=>$dato->getTiempo(),
                'detalle'=>$dato->getDetalle(),
                'foto'=>$request->getUriForPath("/uploads/recetas/".$dato->getFoto()) ,
                'fecha'=>$dato->getFecha()->format('d/m/Y'),
                'categoria_id'=>$dato->GetCategoria()->getId(),
                'categoria'=>$dato->GetCategoria()->getNombre(),
                'usuario_id'=>$dato->GetUsuario()->getId(),
                'usuario'=>$dato->GetUsuario()->getNombre()
                //'fecha'=>$dato->getFecha()->format('H:i:s')
            ];
        }  
        sleep(2);
        return $this->json( $data , $status = Response::HTTP_OK);
    }
   
    #[Route('/recetas/{slug}', methods: ['get'])]
    public function show(Request $request, $slug): JsonResponse
    {
        $datos=$this->em->getRepository(Receta::class)->findBy( array('slug'=>$slug), array());
        if(!$datos)
        {
            
            return $this->json([
                'estado' => 'error',
                'mensaje'=>'La URL no está disponible en este momento'
                
            ], Response::HTTP_NOT_FOUND);  
        }  
        return $this->json([
            'id'=>$datos[0]->getId(),
            'nombre'=> $datos[0]->getNombre(), 
            'slug'=>$datos[0]->getSlug(),
            'tiempo'=>$datos[0]->getTiempo(),
            'detalle'=>$datos[0]->getDetalle(),
            'foto'=>$request->getUriForPath("/uploads/recetas/".$datos[0]->getFoto()) ,
            'fecha'=>$datos[0]->getFecha()->format('d/m/Y'),
            'categoria_id'=>$datos[0]->GetCategoria()->getId(),
            'categoria'=>$datos[0]->GetCategoria()->getNombre(),
            'usuario_id'=>$datos[0]->GetUsuario()->getId(),
            'usuario'=>$datos[0]->GetUsuario()->getNombre()
            
        ], $status = Response::HTTP_OK);
    }
    #[Route('/api/recetas', methods: ['post'])]
    public function create(Request $request, #[MapRequestPayload] RecetaDto $dto  ): JsonResponse
    { 
        //validamos si la categoria existe
        $categoria= $this->em->getRepository(Categoria::class)->find($dto->categoria_id);
        if(!$categoria)
        {
            
            return $this->json([
                'estado' => 'error',
                'mensaje'=>'La URL no está disponible en este momento'
                
            ], Response::HTTP_NOT_FOUND); 
            
        }
        $existe =  $this->em->getRepository(Receta::class)->findBy( array('slug'=>$this->slugger->slug(strtolower($dto->nombre)) ), 
        array()) ;
        if($existe)
        {
            
            return $this->json([
                'estado' => 'error',
                'mensaje'=>'La URL no está disponible en este momento'
                
            ], Response::HTTP_NOT_FOUND); 
            
        }
        /*
        $decode = JWT::decode($request->headers->get('X-AUTH-TOKEN'), new Key($_ENV['JWT_SECRET'], 'HS512'));
        $user = $this->em->getRepository(Usuarios::class)->findOneBy(['id' => $decode->aud]);
        $entity = new Receta();
                $entity->setNombre($dto->nombre);
                $entity->setSlug( $this->slugger->slug(strtolower($dto->nombre)));  
                $entity->setTiempo($dto->tiempo);
                $entity->setCategoria($categoria);
                $entity->setDetalle($dto->detalle);
                $entity->setFecha(new \DateTime());
                $entity->setFoto($newFilename);
                $entity->setUsuario($user);
                $this->em->persist($entity); 
                $this->em->flush();
                return $this->json([
                    'estado' => 'ok',
                    'mensaje'=>'Se creó el registro exitosamente'
                    
                ], Response::HTTP_CREATED);
        */
        $foto = $request->files->get('foto');
        if($foto)
        {
            $newFilename = time().'.'.$foto->guessExtension();
            try {
                $foto->move(
                    $this->getParameter('recetas_directory'),
                    $newFilename
                );
                $decode = JWT::decode($request->headers->get('X-AUTH-TOKEN'), new Key($_ENV['JWT_SECRET'], 'HS512'));
                $user = $this->em->getRepository(Usuarios::class)->findOneBy(['id' => $decode->aud]);
                $entity = new Receta();
                $entity->setNombre($dto->nombre);
                $entity->setSlug( $this->slugger->slug(strtolower($dto->nombre)));  
                $entity->setTiempo($dto->tiempo);
                $entity->setCategoria($categoria);
                $entity->setDetalle($dto->detalle);
                $entity->setFecha(new \DateTime());
                $entity->setFoto($newFilename);
                $entity->setUsuario($user);
                $this->em->persist($entity); 
                $this->em->flush();
                return $this->json([
                    'estado' => 'ok',
                    'mensaje'=>'Se creó el registro exitosamente'
                    
                ], Response::HTTP_CREATED);
            }catch (FileException $e) {
               
                return $this->json([
                    'estado' => 'error',
                    'mensaje' => 'Ups ocurrió un error al intentar subir el archivo',
                ], Response::HTTP_BAD_REQUEST);
            }    
        }else
        {
            return $this->json([
                'estado' => 'error',
                'mensaje'=>'La URL no está disponible en este momento'
                
            ], Response::HTTP_BAD_REQUEST); 
        }
         
    }
    #[Route('/api/recetas/{id}', methods: ['put'])]
    public function update(int $id, Request $request, #[MapRequestPayload] RecetaDto $dto  ): JsonResponse
    { 
        $datos= $this->em->getRepository(Receta::class)->find($id);
        if(!$datos)
        {
            
            return $this->json([
                'estado' => 'error',
                'mensaje'=>'La URL no está disponible en este momento'
                
            ], Response::HTTP_NOT_FOUND);  
        } 
        //validamos si la categoria existe
        $categoria= $this->em->getRepository(Categoria::class)->find($dto->categoria_id);
        if(!$categoria)
        {
            
            return $this->json([
                'estado' => 'error',
                'mensaje'=>'La URL no está disponible en este momento'
                
            ], Response::HTTP_NOT_FOUND); 
            
        }
        $datos->setNombre($dto->nombre);
        $datos->setSlug( $this->slugger->slug(strtolower($dto->nombre)));  
        $datos->setTiempo($dto->tiempo);
        $datos->setCategoria($categoria);
        $datos->setDetalle($dto->detalle); 
         
        
        //$this->em->persist($entity); 
        $this->em->flush();
        return $this->json([
            'estado' => 'ok',
            'mensaje'=>'Se modificó el registro exitosamente'
            
        ], RESPONSE::HTTP_OK); 
    }
    #[Route('/api/recetas/{id}', methods: ['delete'])]
    public function destroy(Request $request, int $id ): JsonResponse
    {
        $datos= $this->em->getRepository(Receta::class)->find($id);
        if(!$datos)
        {
            
            return $this->json([
                'estado' => 'error',
                'mensaje'=>'La URL no está disponible en este momento'
                
            ], RESPONSE::HTTP_NOT_FOUND);  
        } 
        unlink(getcwd().'/uploads/recetas/'.$datos->getFoto());
        $this->em->remove($datos);
        $this->em->flush();
        return $this->json([
            'estado' => 'ok',
            'mensaje'=>'Se eliminó el registro exitosamente'
        ], RESPONSE::HTTP_OK);
    }
    
    
}
