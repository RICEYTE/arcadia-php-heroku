<?php

namespace App\Controller;

use App\Entity\Animal;
use App\Repository\AnimalRepository;
use Doctrine\ORM\EntityManagerInterface;
use OpenApi\Attributes\Get;
use OpenApi\Attributes\JsonContent;
use OpenApi\Attributes\Post;
use OpenApi\Attributes\RequestBody;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\SerializerInterface;

#[Route('/api/animal',name: 'app_animal_')]
class AnimalController extends AbstractController
{

    private $manager;
    private $repository;
    private $serializer;

    public function __construct(
        EntityManagerInterface $manager,
        AnimalRepository $repository,
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
        $animalsList = $this->repository->findAll();

        if($animalsList){
            $data = $this->serializer->serialize($animalsList,'json');
            $code_http= Response::HTTP_OK;
        }
        else{
            $data = $this->serializer->serialize("Aucun animal trouvé!",'json');
            $code_http= Response::HTTP_NOT_FOUND;
        }

        $jsonResponse = new JsonResponse($data,$code_http,[],'true');

        return $jsonResponse;
    }



    #[Route('/{prenom}', name: 'getByPrenom',methods: 'GET')]
    #[Get(
        path: '/api/animal/{prenom}',
        description: "Recherche d'un animal par son prénom. Entrer le prénom de l'animal",
        summary: 'Recherche d\'un animal par son prénom',
    )]
    #[\OpenApi\Attributes\Response(
        response: "200",
        description: "L'animal a été trouvé"
    )]
    #[\OpenApi\Attributes\Response(
        response: "404",
        description: "Animal non trouvé."
    )]
    public function  getByPrenom(string $prenom):JsonResponse
    {
        $animal = $this->repository->findOneBy(['prenom'=> $prenom]);

        if($animal){
            $data = $this->serializer->serialize($animal,'json');
            $code_http= Response::HTTP_OK;
        }
        else{
            $data = $this->serializer->serialize("animal $prenom non trouvé!",'json');
            $code_http= Response::HTTP_NOT_FOUND;
        }

        $jsonResponse = new JsonResponse($data,$code_http,[],'true');

        return $jsonResponse;
    }
    #[Route('/{id}', name: 'deleteById',methods: 'DELETE')]
    public function  deleteById(int $id):JsonResponse
    {
        $animal = $this->repository->findOneBy(['animal_id'=> $id]);

        if($animal){

            $this->manager->remove($animal);
            $this->manager->flush();
            $data = $this->serializer->serialize("animal $id supprimmé !",'json');
            $code_http= Response::HTTP_OK;
        }
        else{
            $data = $this->serializer->serialize("animal $id non trouvé!",'json');
            $code_http= Response::HTTP_NOT_FOUND;
        }

        $jsonResponse = new JsonResponse($data,$code_http,[],'true');

        return $jsonResponse;
    }

    #[Route('/', name: 'create',methods: 'POST')]
    public function  create(Request $request):JsonResponse
    {

        $animal = $this->serializer->deserialize($request->getContent(),Animal::class,'json');

        if($animal){

            $this->manager->persist($animal);
            $this->manager->flush();
            $data = $this->serializer->serialize($animal,'json');
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
        $animal = $this->repository->findOneBy(['animal_id'=> $id]);

        $animal_request = $this->serializer->deserialize($request->getContent(),Animal::class,'json');

        if($animal){

            $animal->setNom($animal_request->getNom());
            $animal->setDescription($animal_request->getDescription());
            $animal->setCommentaireanimal($animal_request->getCommentaireanimal());
            $this->manager->persist($animal);
            $this->manager->flush();
            $data = $this->serializer->serialize($animal,'json');
            $code_http= Response::HTTP_OK;
        }
        else{
            $data = $this->serializer->serialize("animal $id non trouvé!",'json');
            $code_http= Response::HTTP_BAD_REQUEST;
        }

        $jsonResponse = new JsonResponse($data,$code_http,[],'true');

        return $jsonResponse;
    }
}
