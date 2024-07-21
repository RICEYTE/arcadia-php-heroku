<?php

namespace App\Controller;

use App\Entity\Animal;
use App\Entity\Habitat;
use App\Entity\Race;
use App\Repository\AnimalRepository;
use App\Repository\HabitatRepository;
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

#[Route('/api/animal',name: 'app_animal_')]
class AnimalController extends AbstractController
{

    private $manager;
    private $repository;
    private $serializer;
    private $habitatRepository;
    private $raceRepository;

    public function __construct(
        EntityManagerInterface $manager,
        AnimalRepository $repository,
        HabitatRepository $habitatRepository,
        RaceRepository $raceRepository,
        SerializerInterface $serializer,
    )

    {
        $this->repository = $repository;
        $this->manager = $manager;
        $this->habitatRepository = $habitatRepository;
        $this->raceRepository = $raceRepository;
        $this->serializer = $serializer;

    }

    #[Route('/',name: 'showAll',methods: 'GET')]
    #[Get(
        path: '/api/animal/',
        description: "Recherche de tous les animaux",
        summary: 'Récupérer la liste des animaux',
    )]
    #[\OpenApi\Attributes\Response(
        response: "200",
        description: "Animaux trouvés"
    )]
    #[\OpenApi\Attributes\Response(
        response: "404",
        description: "Aucun animal trouvé."
    )]
    public function  getAll():JsonResponse
    {
        return $this->json($this->repository->findAll(), 200, [],  ['groups'=>'animal_read']);
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
            $data = $animal;
            $code_http= Response::HTTP_OK;
        }
        else{
            $data = $this->serializer->serialize("animal $prenom non trouvé!",'json');
            $code_http= Response::HTTP_NOT_FOUND;
        }

        return $this->json($data, $code_http, [],  ['groups'=>'animal_read']);
    }
    #[Route('/{prenom}', name: 'deleteByPrenom',methods: 'DELETE')]
    #[Delete(
        path: '/api/animal/{prenom}',
        description: "Recherche d'un animal par son prénom. Entrer le prénom de l'animal",
        summary: 'Recherche d\'un animal par son prénom',
    )]
    #[\OpenApi\Attributes\Response(
        response: "200",
        description: "L'animal a été supprimé"
    )]
    #[\OpenApi\Attributes\Response(
        response: "404",
        description: "Animal non trouvé."
    )]
    public function  deleteById(string $prenom):JsonResponse
    {
        $animal = $this->repository->findOneBy(['prenom'=> $prenom]);

        if($animal){

            $this->manager->remove($animal);
            $this->manager->flush();
            $data = $this->serializer->serialize("animal $prenom supprimmé !",'json');
            $code_http= Response::HTTP_OK;
        }
        else{
            $data = $this->serializer->serialize("animal $prenom non trouvé!",'json');
            $code_http= Response::HTTP_NOT_FOUND;
        }

        return new JsonResponse($data,$code_http,[],'true');


    }

    #[Route('/', name: 'create',methods: 'POST')]
    #[Post(
        path: '/api/animal/',
        description: "Ajout d'un animal.",
        summary: 'Ajouter un nouvel animal',
        requestBody: new RequestBody(
            content: new JsonContent(
                properties: [
                    new \OpenApi\Attributes\Property(
                        "prenom",
                        example: "Léo"
                    ),
                    new \OpenApi\Attributes\Property(
                        "etat",
                        example: "OK"
                    ),
                    new \OpenApi\Attributes\Property(
                        "habitat",
                        example: "Savane"
                    ),
                    new \OpenApi\Attributes\Property(
                        "race",
                        example: "Lion"
                    )
                ]
            )
        )
    )]
    #[\OpenApi\Attributes\Response(
        response: "200",
        description: "Animal ajouté"
    )]
    #[\OpenApi\Attributes\Response(
        response: "400",
        description: "La requête n'est pas correcte."
    )]
    public function  create(Request $request):JsonResponse
    {

        $animal = $this->getAnimal($request);


        if($animal){
            $data = $this->serializer->serialize("Animal existe déjà",'json');
            $code_http = Response::HTTP_BAD_REQUEST;
        }
        else {

            $race = $this->getRace($request);
            $habitat = $this->getHabitat($request);
            $animal_new = $this->serializer->deserialize($request->getContent(),Animal::class,'json');


            if ($animal_new and $race!=null and $habitat!=null) {

                $animal_new->setHabitat($habitat);
                $animal_new->setRace($race);

                $this->manager->persist($animal_new);
                $this->manager->flush();


                $data = $this->serializer->serialize("Animal créé", 'json');
                $code_http = Response::HTTP_CREATED;
            } else {
                $data = $this->serializer->serialize("Création impossible!", 'json');
                $code_http = Response::HTTP_BAD_REQUEST;
            }
    }
        $jsonResponse = new JsonResponse($data,$code_http,[],'true');

        return $jsonResponse;

    }


    private function getAnimals() : array
    {

        return $this ->repository->findAll();
    }

    private function getHabitats() : array
    {

        return $this ->habitatRepository->findAll();
    }
    private function getRaces() : array
    {

        return $this ->raceRepository->findAll();
    }

    private function getAnimal(Request $request): ?Animal{

        $request_content = $request->getContent();
        $animals= $this->getAnimals();
        $existAnimal = false;
        $index=0;
        while($index < count($animals) and !$existAnimal){
            $animal = $animals[$index];
            $animal_name = $animal->getPrenom();
            $existAnimal = preg_match("/$animal_name/",$request_content,$request_array_animal,0,0);

            $index++;
        }
        if($existAnimal){
        return $animals[$index-1];}
        else{
            return null;
        }
    }

    private function getHabitat(Request $request): ?Habitat
    {
        $request_content = $request->getContent();
        $habitats= $this->getHabitats();
        $existHabitat = false;
        $index=0;
        while($index < count($habitats) and !$existHabitat){
            $habitat_name = $habitats[$index]->getNom();
            $existHabitat = preg_match("/$habitat_name/",$request_content,$request_array_habitat,0,0);

            $index++;
        }
        if($existHabitat){
            return $habitats[$index-1];}
        else{
            return null;
        }
    }



    private function getRace(Request $request): ?Race
    {
        $request_content = $request->getContent();
        $races= $this->getRaces();
        $existRace = false;
        $indexRace=0;
        while($indexRace < count($races) and !$existRace){
            $race_name = $races[$indexRace]->getLabel();
            $existRace = preg_match("/$race_name/",$request_content,$request_array_race,0,0);

            $indexRace++;
        }
        if($existRace) {
            return $races[$indexRace - 1];
        }
        else{
            return null;
        }
}
    #[Route('/{prenom}', name: 'editByPrenom',methods: 'PUT')]
    #[Put(
        path: '/api/animal/{prenom}',
        description: "Modification d'un animal.",
        summary: 'Modifier un animal. ',
        requestBody: new RequestBody(
            content: new JsonContent(
                properties: [
                    new \OpenApi\Attributes\Property(
                        "etat",
                        example: "Malade"
                    )
                ]
            )
        )
    )]
    #[\OpenApi\Attributes\Response(
        response: "200",
        description: "Animal modifié"
    )]
    #[\OpenApi\Attributes\Response(
        response: "400",
        description: "La requête n'est pas correcte."
    )]
    #[\OpenApi\Attributes\Response(
        response: "404",
        description: "Animal non trouvé."
    )]
    public function  editByPrenom(Request $request,string $prenom):JsonResponse
    {
        $animal = $this->repository->findOneBy(['prenom'=> $prenom]);

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
            $data = $this->serializer->serialize("animal $prenom non trouvé!",'json');
            $code_http= Response::HTTP_BAD_REQUEST;
        }

        $jsonResponse = new JsonResponse($data,$code_http,[],'true');

        return $jsonResponse;
    }
}
