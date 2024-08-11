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
use Firebase\JWT\JWT; 
use Firebase\JWT\Key;

class RecetasHelperController extends AbstractController
{
    private $em;
    private $slugger;

    public function __construct(EntityManagerInterface $em , SluggerInterface $slugger )
    {
        $this->em=$em;
        $this->slugger=$slugger;
    }
    #[Route('/api/recetas-panel', methods: ['get'])]
    public function recetas_panel(Request $request): JsonResponse
    {
        if(empty($request->headers->get('X-AUTH-TOKEN')))
        {
            return $this->json([
                'estado' => 'error',
                'mensaje'=>'La URL no está disponible en este momento'
                
            ], Response::HTTP_NOT_FOUND); 
        }
        $decode = JWT::decode($request->headers->get('X-AUTH-TOKEN'), new Key($_ENV['JWT_SECRET'], 'HS512'));
        $user = $this->em->getRepository(Usuarios::class)->findOneBy(['id' => $decode->aud]);
        $datos= $this->em->getRepository(Receta::class)->findBy(array('usuario'=>$user), array('id'=>'desc'));
        if(sizeof($datos)==0)
        {
            $data=[];
        }else
        {
            foreach($datos as $dato)
            { 
                $data[]=
                [
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
        }
        
        return $this->json($data, $status = Response::HTTP_OK);
    }
    #[Route('/recetas-home', methods: ['get'])]
    public function para_home(Request $request): JsonResponse
    {
        
        $datos= $this->em->getRepository(Receta::class)->findBy(array(), array('id'=>'desc'), 3);
        foreach($datos as $dato)
        { 
            $data[]=
            [
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
        return $this->json($data, $status = Response::HTTP_OK);
    }
    #[Route('/recetas-search', methods: ['get'])]
    public function productos_buscador(Request $request): JsonResponse
    {
        $search=$_GET['search']; 
        $categoria= $this->em->getRepository(Categoria::class)->find($_GET['categoria_id']);
        if(!$categoria)
        {
            
            return $this->json([
                'estado' => 'error',
                'mensaje'=>'La URL no está disponible en este momento'
                
            ], Response::HTTP_NOT_FOUND); 
            
        }
        $datos = $this->em->getRepository(Receta::class)
                           ->createQueryBuilder('r')
                           ->andWhere('r.nombre LIKE :search')
                           ->setParameter('search', '%'.$search.'%') 
                           ->andWhere('r.categoria ='.$_GET['categoria_id'])
                           ->getQuery()
                           ->getResult();
        if(sizeof($datos)==0)
        {
            $data=[];
        }else
        {
            foreach($datos as $dato)
            { 
                $data[]=
                    [
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
                    ];
            } 
        }
        
        return $this->json($data, $status = Response::HTTP_OK);
    }
    #[Route('/api/recetas-fotos', methods: ['post'])]
    public function photos(Request $request   ): JsonResponse
    { 
        //validamos si la categoria existe
        $datos= $this->em->getRepository(Receta::class)->find($request->request->get('id'));
        if(!$datos)
        {
            
            return $this->json([
                'estado' => 'error',
                'mensaje'=>'La URL no está disponible en este momento'
                
            ], Response::HTTP_NOT_FOUND); 
            
        }
        $foto = $request->files->get('foto');
        if($foto)
        {
            $newFilename = time().'.'.$foto->guessExtension();
            try {
                $foto->move(
                    $this->getParameter('recetas_directory'),
                    $newFilename
                ); 
                unlink(getcwd().'/uploads/recetas/'.$datos->getFoto());
                $datos->setFoto($newFilename);
                $this->em->persist($datos); 
                $this->em->flush();
                return $this->json([
                    'estado' => 'ok',
                    'mensaje'=>'Se modificó el registro exitosamente'
                    
                ], Response::HTTP_OK);
            }catch (FileException $e) {
               
                return $this->json([
                    'estado' => 'error',
                    'mensaje' => 'Ocurrió un error inesperado',
                ], Response::HTTP_BAD_REQUEST);
            }    
        }else
        {
            return $this->json([
                'estado' => 'error',
                'mensaje' => 'Ocurrió un error inesperado',
            ], Response::HTTP_BAD_REQUEST);
        }
    }
}
