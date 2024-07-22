<?php

namespace App\Controller;

use App\Entity\Race;
use App\Repository\RaceRepository;
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

#[Route('/api/race',name: 'app_race_')]
class RaceController extends AbstractController
{

    private $manager;
    private $repository;
    private $serializer;

    public function __construct(
        EntityManagerInterface $manager,
        RaceRepository $repository,
        SerializerInterface $serializer,
    )

    {
        $this->repository = $repository;
        $this->manager = $manager;
        $this->serializer = $serializer;
    }

    #[Route('/',name: 'showAll',methods: 'GET')]
    #[Get(
        path: "/api/race/",
        description: "Récupération de la liste des races",
        summary: "Récupérer la liste des races",

    )]
    #[\OpenApi\Attributes\Response(
        response: "200",
        description: "Les races sont affichés !"
    )]
    public function  getAll():JsonResponse
    {
        return $this->json($this->repository->findAll(),200,[],['groups'=>'race_read']);
    }

    #[Route('/{label}', name: 'getByLabel',methods: 'GET')]
    #[Get(
        path: "/api/race/{label}",
        description: "Récupération d'une race par son label",
        summary: "récupération d'une race par son label",

    )]
    #[\OpenApi\Attributes\Response(
        response: "200",
        description: "La race existe et est  sont affichée !"
    )]
    #[\OpenApi\Attributes\Response(
        response: "404",
        description: "La race n'existe pas !"
    )]
    public function  getByLabel(string $label ):JsonResponse
    {
        $race = $this->repository->findOneBy(['label'=> $label]);

        if($race){
            $code_http= Response::HTTP_OK;
        }
        else{
            $code_http= Response::HTTP_NOT_FOUND;
        }

        return $this->json($race,$code_http,['groups'=>'race_read']);


    }

    #[Route('/{label}', name: 'deleteByLabel',methods: 'DELETE')]
    #[Delete(
        path: "/api/race/{label}",
        description: "Suppression d'une race à partir de son label",
        summary: "Suppression d'une race à partir de son label",

    )]
    #[\OpenApi\Attributes\Response(
        response: "200",
        description: "La race est supprimée!"
    )]
    #[\OpenApi\Attributes\Response(
        response: "404",
        description: "La race n'existe pas!"
    )]
    public function  deleteByLabel(string $label):JsonResponse
    {
        $race = $this->repository->findOneBy(['label'=> $label]);

        if($race){

            $this->manager->remove($race);
            $this->manager->flush();
            $code_http= Response::HTTP_OK;
        }
        else{
            $code_http= Response::HTTP_NOT_FOUND;
        }

       return $this->json($race,$code_http,['groups'=>'race_read']);

    }

    #[Route('/', name: 'create',methods: 'POST')]
    #[Post(
        path: "/api/race/",
        description: "Ajouter une race.",
        summary: "Ajouter un nouvelle race",
        requestBody: new RequestBody(
            content: new JsonContent(
                properties: [
                    new \OpenApi\Attributes\Property(
                        "label",
                        example: "Lion"
                    )
                ]
            )
        )
    )]
    #[\OpenApi\Attributes\Response(
        response: "200",
        description: "La race est ajoutée !"
    )]
    #[\OpenApi\Attributes\Response(
        response: "409",
        description: "La race existe déjà !"
    )]
    public function  create(Request $request):JsonResponse
    {

        $race_new = $this->serializer->deserialize($request->getContent(),Race::class,'json');
        $race = $this->repository->findOneBy(['label'=>$race_new->getLabel()]);

        if($race){
            $code_http= Response::HTTP_CONFLICT;
        }
        else
        if($race_new){

            $this->manager->persist($race_new);
            $this->manager->flush();
            $code_http= Response::HTTP_CREATED;
        }
        else{
            $code_http= Response::HTTP_BAD_REQUEST;
        }

        return $this->json($race_new,$code_http,['groups'=>'race_read']);
    }


    #[Route('/{label}', name: 'editByLabel',methods: 'PUT')]
    #[Put(
        path: "/api/race/{label}",
        description: "Modifier une race.",
        summary: "Modifier une race identifiée par son label",
        requestBody: new RequestBody(
            content: new JsonContent(
                properties: [
                    new \OpenApi\Attributes\Property(
                        "label",
                        example: "Crocodile"
                    )
                ]
            )
        )
    )]
    #[\OpenApi\Attributes\Response(
        response: "200",
        description: "La race est modifiée !"
    )]
    #[\OpenApi\Attributes\Response(
        response: "404",
        description: "La race n'existe pas!"
    )]
    #[\OpenApi\Attributes\Response(
        response: "409",
        description: "Impossible de dupliquer une race.La race existe déjà!"
    )]
    public function  editByLabel(Request $request,string $label):JsonResponse
    {
        $race_a_modifier = $this->repository->findOneBy(['label'=> $label]);
        $race_request = $this->serializer->deserialize($request->getContent(),Race::class,'json');
        $existeRace = $this->repository->findOneBy(['label'=>$race_request->getLabel()]);


            if ($race_a_modifier) {

                if($existeRace){
                    $code_http= Response::HTTP_CONFLICT;
                }
                else
                {
                    $race_a_modifier->setLabel($race_request->getLabel());
                    $this->manager->persist($race_a_modifier);
                    $this->manager->flush();
                    $code_http = Response::HTTP_OK;
                }
            }
            else
            {
                $code_http = Response::HTTP_NOT_FOUND;
            }

        return $this->json($race_a_modifier,$code_http,['groups'=>'race_read']);
    }
}
