<?php

namespace App\Controller;

use App\Entity\Habitat;
use App\Repository\HabitatRepository;
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

#[Route('/api/habitat',name: 'app_habitat_')]
class HabitatController extends AbstractController
{

    private $manager;
    private $repository;
    private $serializer;

    public function __construct(
        EntityManagerInterface $manager,
        HabitatRepository $repository,
        SerializerInterface $serializer,
    )

    {
        $this->repository = $repository;
        $this->manager = $manager;
        $this->serializer = $serializer;
    }

    #[Route('/',name: 'showAll',methods: 'GET')]
    #[Get(
        path: '/api/habitat/',
        description: "Récupération de tous les habitats.",
        summary: 'Recherche de tous les habitats',
    )]
    #[\OpenApi\Attributes\Response(
        response: "200",
        description: "Habitats trouvés"
    )]
    #[\OpenApi\Attributes\Response(
        response: "404",
        description: "Habitats non trouvés."
    )]
    public function  getAll():JsonResponse
    {

       return $this->json($this->repository->findAll(),200,[],['groups'=>'habitat_read']);


    }

    #[Route('/{nom}', name: 'getByNom',methods: 'GET')]
    #[Get(
        path: '/api/habitat/{nom}',
        description: "Recherche d'un habitat par son nom. Entrer le nom de l'habitat",
        summary: 'Recherche d\'un habitat par son nom',
    )]
    #[\OpenApi\Attributes\Response(
        response: "200",
        description: "Habitat trouvé"
    )]
    #[\OpenApi\Attributes\Response(
        response: "404",
        description: "Habitat non trouvé."
    )]
    public function  getByName(string $nom):JsonResponse
    {
        $habitat = $this->repository->findOneBy(['nom' => $nom]);

        if($habitat){
            $code_http= Response::HTTP_OK;
        }
        else{
            $code_http= Response::HTTP_NOT_FOUND;
        }

        return $this->json($habitat,$code_http,['groups'=> 'habitat_read']);


    }
    #[Route('/{nom}', name: 'deleteByNom',methods: 'DELETE')]
    #[Delete(
        path: '/api/habitat/{nom}',
        description: "Suppression d'un habitat par son nom. Entrer le nom de l'habitat",
        summary: 'Suppression d\'un habitat par son nom',
    )]
    #[\OpenApi\Attributes\Response(
        response: "200",
        description: "Habitat supprimé"
    )]
    #[\OpenApi\Attributes\Response(
        response: "404",
        description: "Habitat non trouvé."
    )]
    public function  deleteByNom(string $nom):JsonResponse
    {
        $habitat = $this->repository->findOneBy(['nom'=> $nom]);

        if($habitat){

            $this->manager->remove($habitat);
            $this->manager->flush();
            $code_http= Response::HTTP_OK;
        }
        else{
            $code_http= Response::HTTP_NOT_FOUND;
        }

        return $this->json($habitat,$code_http,['groups'=> 'habitat_read']);
    }

    #[Route('/', name: 'create',methods: 'POST')]
    #[Post(
        path: '/api/habitat/',
        description: "Ajout d'un habitat.",
        summary: 'Ajouter un nouvel habitat',
        requestBody: new RequestBody(
            content: new JsonContent(
                properties: [
                    new \OpenApi\Attributes\Property(
                        "nom",
                        example: "Savane"
                    ),
                    new \OpenApi\Attributes\Property(
                        "description",
                        example: "La savane est grande ..."
                    )
                ]
            )
        )
    )]
    #[\OpenApi\Attributes\Response(
        response: "201",
        description: "Habitat ajouté"
    )]
    #[\OpenApi\Attributes\Response(
        response: "500",
        description: "La requête n'est pas valide"
    )]
    public function  create(Request $request):JsonResponse
    {

        $habitat = $this->serializer->deserialize($request->getContent(),Habitat::class,'json');
        $habitatAlreadyExist = $this->repository->findOneBy(['nom'=> $habitat->getNom()]);
        if($habitatAlreadyExist){
            $code_http= Response::HTTP_CONFLICT;
        }
        else
        if($habitat){

            $this->manager->persist($habitat);
            $this->manager->flush();
            $code_http= Response::HTTP_CREATED;
        }
        else{
            $code_http= Response::HTTP_BAD_REQUEST;
        }

        return $this->json($habitat,$code_http,['groups'=> 'habitat_read']);
    }


    #[Route('/{nom}', name: 'editByName',methods: 'PUT')]
    #[Put(
        path: '/api/habitat/{nom}',
        description: "Modification d'un habitat.",
        summary: "Modification d'un habitat",
        requestBody: new RequestBody(
            content: new JsonContent(
                properties: [
                    new \OpenApi\Attributes\Property(
                        "description",
                        example: "Cet habitat est grand ..."
                    )
                ]
            )
        )
    )]
    #[\OpenApi\Attributes\Response(
        response: "200",
        description: "Habitat modifié"
    )]
    public function  editByName(Request $request,string $nom):JsonResponse
    {
        $habitat = $this->repository->findOneBy(['nom'=> $nom]);

        $habitat_request = $this->serializer->deserialize($request->getContent(),Habitat::class,'json');

        if($habitat){

            $habitat->setNom($habitat_request->getNom());
            $habitat->setDescription($habitat_request->getDescription());
            if($habitat_request->getCommentaireHabitat()){
            $habitat->setCommentaireHabitat($habitat_request->getCommentaireHabitat());
            }
            $this->manager->persist($habitat);
            $this->manager->flush();
            $code_http= Response::HTTP_OK;
        }
        else{
            $code_http= Response::HTTP_NOT_FOUND;
        }

        return $this->json($habitat,$code_http,['groups'=> 'habitat_read']);
    }
}
