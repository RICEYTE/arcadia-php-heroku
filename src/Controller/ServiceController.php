<?php

namespace App\Controller;

use App\Entity\service
;
use App\Repository\ServiceRepository;
use Doctrine\ORM\EntityManagerInterface;
use OpenApi\Attributes\Delete;
use OpenApi\Attributes\Get;
use OpenApi\Attributes\JsonContent;
use OpenApi\Attributes\Post;
use OpenApi\Attributes\Put;
use OpenApi\Attributes\RequestBody;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\SerializerInterface;

#[Route('/api/service',name: 'app_service_')]
class ServiceController extends AbstractController
{
    private $manager;
    private $repository;
    private $serializer;
    public function __construct(
        EntityManagerInterface $manager,
        SerializerInterface $serializer,
        ServiceRepository $repository
    )
    {
        $this->repository = $repository;
        $this->manager = $manager;
        $this->serializer = $serializer;
    }

    #[Route('/',name: 'getAll',methods: 'GET')]
    #[Get(
        path: "/api/service/",
        description: "Récupération de la liste des services",
        summary: "Récupérer la liste des services",

    )]
    #[\OpenApi\Attributes\Response(
        response: "200",
        description: "Les services sont affichés !"
    )]
    public function  getAll():JsonResponse
    {

        return $this->json($this->repository->findAll(),200,['groups'=>'service_read']);
    }

    #[Route('/{nom}', name: 'getByNom',methods: 'GET')]
    #[Get(
        path: "/api/service/{nom}",
        description: "Récupération d'un service identifié par son nom",
        summary: "Récupérer un service identifié par son nom",

    )]
    #[\OpenApi\Attributes\Response(
        response: "200",
        description: "Les service est affiché !"
    )]
    #[\OpenApi\Attributes\Response(
        response: "404",
        description: "Les service n'existe pas !"
    )]
    public function  getByNom(string $nom):JsonResponse
    {
        $service = $this->repository->findOneBy(['nom'=> $nom]);

        if($service){
            $code_http= Response::HTTP_OK;
        }
        else{
            $code_http= Response::HTTP_NOT_FOUND;
        }
        return $this->json($service,$code_http,['groups'=>'service_read']);
    }

    #[Route('/{nom}', name: 'deleteByNom',methods: 'DELETE')]
    #[Delete(
        path: "/api/service/{nom}",
        description: "Suppression d'un service à partir de son nom",
        summary: "Suppression d'un service à partir de son nom",

    )]
    #[\OpenApi\Attributes\Response(
        response: "200",
        description: "Le service est supprimé!"
    )]
    #[\OpenApi\Attributes\Response(
        response: "404",
        description: "Le service n'existe pas!"
    )]
    public function  deleteByNom(string $nom):JsonResponse
    {
        $service = $this->repository->findOneBy(['nom'=> $nom]);

        if($service){

            $this->manager->remove($service);
            $this->manager->flush();
            $code_http= Response::HTTP_OK;
        }
        else{
            $code_http= Response::HTTP_NOT_FOUND;
        }

        return $this->json($service,$code_http,['groups'=>'service_read']);
    }

    #[Route('/', name: 'create',methods: 'POST')]
    #[Post(
        path: "/api/service/",
        description: "Ajouter un service.",
        summary: "Ajouter un nouveau service",
        requestBody: new RequestBody(
            content: new JsonContent(
                properties: [
                    new \OpenApi\Attributes\Property(
                        "nom",
                        example: "Petit train"
                    ),
                    new \OpenApi\Attributes\Property(
                        "description",
                        example: "Venez profiter d'une visite en petit train pour ne rien manquer !"
                    )
                ]
            )
        )
    )]
    #[\OpenApi\Attributes\Response(
        response: "200",
        description: "Le service est ajouté !"
    )]
    #[\OpenApi\Attributes\Response(
        response: "409",
        description: "Le service existe déjà !"
    )]
    public function  create(Request $request):JsonResponse
    {

        $service = $this->serializer->deserialize($request->getContent(),Service::class,'json');

        $existeService = $this->repository->findOneBy(['nom'=>$service->getNom()]);
        if($service){

            if($existeService){
                $code_http= Response::HTTP_CONFLICT;
            }
            else{
                $this->manager->persist($service);
                $this->manager->flush();
                $code_http= Response::HTTP_CREATED;
            }

        }
        else{

            $code_http= Response::HTTP_BAD_REQUEST;
        }

        return $this->json($service,$code_http,['groups'=>'service_read']);
    }


    #[Route('/{nom}', name: 'editByNom',methods: 'PUT')]
    #[Put(
        path: "/api/service/{nom}",
        description: "Modifier un role utilisateur.",
        summary: "Modifier un role utilisateur",
        requestBody: new RequestBody(
            content: new JsonContent(
                properties: [
                    new \OpenApi\Attributes\Property(
                        "nom",
                        example: "nom modifie"
                    ),
                    new \OpenApi\Attributes\Property(
                        "description",
                        example: "Description modifiée"
                    ),
                ]
            )
        )
    )]
    #[\OpenApi\Attributes\Response(
        response: "200",
        description: "Le service est modifié !"
    )]
    #[\OpenApi\Attributes\Response(
        response: "404",
        description: "Le service n'existe pas!"
    )]
    public function  editByNom(Request $request,string $nom):JsonResponse
    {
        $service = $this->repository->findOneBy(['nom'=> $nom]);

        $service_request = $this->serializer->deserialize($request->getContent(),Service::class,'json');

        $existeServiceAvecLeMemeNom = $this->repository->findOneBy(['nom'=> $service_request->getNom()]);

        if($service){

            if($existeServiceAvecLeMemeNom){
                $code_http= Response::HTTP_CONFLICT;
            }
            else{
                if($service_request->getNom()){
                    $service->setNom($service_request->getNom());
                }
                if($service_request->getDescription()){
                    $service->setDescription($service_request->getDescription());
                }

                $this->manager->persist($service);
                $this->manager->flush();
                $code_http= Response::HTTP_OK;
            }

        }
        else{
            $code_http= Response::HTTP_BAD_REQUEST;
        }

        return $this->json($service,$code_http,['groups'=>'service_read']);
    }
}
