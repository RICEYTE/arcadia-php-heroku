<?php

namespace App\Controller;

use App\Entity\Habitat;
use App\Repository\HabitatRepository;
use Doctrine\ORM\EntityManagerInterface;
use OpenApi\Attributes\Get;
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
    public function  getAll():JsonResponse
    {
        $habitatsList = $this->repository->findAll();

        if($habitatsList){
            $data = $this->serializer->serialize($habitatsList,'json');
            $code_http= Response::HTTP_OK;
        }
        else{
            $data = $this->serializer->serialize("Aucun habitat trouvé!",'json');
            $code_http= Response::HTTP_NOT_FOUND;
        }

        $jsonResponse = new JsonResponse($data,$code_http,[],'true');

        return $jsonResponse;
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
            $data = $this->serializer->serialize($habitat,'json');
            $code_http= Response::HTTP_OK;
        }
        else{
            $data = $this->serializer->serialize("Habitat $nom non trouvé!",'json');
            $code_http= Response::HTTP_NOT_FOUND;
        }

        $jsonResponse = new JsonResponse($data,$code_http,[],'true');

        return $jsonResponse;
    }
    #[Route('/{id}', name: 'deleteById',methods: 'DELETE')]
    public function  deleteById(int $id):JsonResponse
    {
        $habitat = $this->repository->findOneBy(['id'=> $id]);

        if($habitat){

            $this->manager->remove($habitat);
            $this->manager->flush();
            $data = $this->serializer->serialize("Habitat $id supprimmé !",'json');
            $code_http= Response::HTTP_OK;
        }
        else{
            $data = $this->serializer->serialize("Habitat $id non trouvé!",'json');
            $code_http= Response::HTTP_NOT_FOUND;
        }

        $jsonResponse = new JsonResponse($data,$code_http,[],'true');

        return $jsonResponse;
    }

    #[Route('/', name: 'create',methods: 'POST')]
    public function  create(Request $request):JsonResponse
    {

        $habitat = $this->serializer->deserialize($request->getContent(),Habitat::class,'json');

        if($habitat){

            $this->manager->persist($habitat);
            $this->manager->flush();
            $data = $this->serializer->serialize($habitat,'json');
            $code_http= Response::HTTP_CREATED;
        }
        else{
            $data = $this->serializer->serialize("Création impossible!",'json');
            $code_http= Response::HTTP_BAD_REQUEST;
        }

        $jsonResponse = new JsonResponse($data,$code_http,[],'true');

        return $jsonResponse;
    }


    #[Route('/{id}', name: 'editById',methods: 'PUT')]
    public function  editById(Request $request,int $id):JsonResponse
    {
        $habitat = $this->repository->findOneBy(['id'=> $id]);

        $habitat_request = $this->serializer->deserialize($request->getContent(),Habitat::class,'json');

        if($habitat){

            $habitat->setNom($habitat_request->getNom());
            $habitat->setDescription($habitat_request->getDescription());
            $habitat->setCommentaireHabitat($habitat_request->getCommentaireHabitat());
            $this->manager->persist($habitat);
            $this->manager->flush();
            $data = $this->serializer->serialize($habitat,'json');
            $code_http= Response::HTTP_OK;
        }
        else{
            $data = $this->serializer->serialize("Habitat $id non trouvé!",'json');
            $code_http= Response::HTTP_BAD_REQUEST;
        }

        $jsonResponse = new JsonResponse($data,$code_http,[],'true');

        return $jsonResponse;
    }
}
