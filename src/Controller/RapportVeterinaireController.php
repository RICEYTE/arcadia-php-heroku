<?php

namespace App\Controller;

use App\Entity\RapportVeterinaire;
use App\Repository\RapportVeterinaireRepository;
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

#[Route('/api/rapportVeterinaire',name: 'app_rapportVeterinaire_')]
class RapportVeterinaireController extends AbstractController
{

    private $manager;
    private $repository;
    private $serializer;

    public function __construct(
        EntityManagerInterface $manager,
        RapportVeterinaireRepository $repository,
        SerializerInterface $serializer,
    )

    {
        $this->repository = $repository;
        $this->manager = $manager;
        $this->serializer = $serializer;
    }

    #[Route('/',name: 'showAll',methods: 'GET')]
    #[Get(
        path: "/api/rapportVeterinaire/",
        description: "Récupération de la liste des rapportVeterinaires",
        summary: "Récupérer la liste des rapportVeterinaires",

    )]
    #[\OpenApi\Attributes\Response(
        response: "200",
        description: "Les la liste des rapportVeterinaires sont affichés !"
    )]
    public function  getAll():JsonResponse
    {
        return $this->json($this->repository->findAll(),200,[],['groups'=>'veterinaire_read']);
    }

    #[Route('/{id}', name: 'getById',methods: 'GET')]
    #[Get(
        path: "/api/rapportVeterinaire/{id}",
        description: "Récupération d'un rapportveterinaire identifie par son id",
        summary: "Récupérer d'un rapportveterinaire identifie par son id",

    )]
    #[\OpenApi\Attributes\Response(
        response: "200",
        description: "Le rapportVeterinaire est trouvé et affihé!"
    )]
    #[\OpenApi\Attributes\Response(
        response: "404",
        description: "Le rapportVeterinaire n'existe pas!"
    )]
    public function  getById(int $id):JsonResponse
    {
        $rapportVeterinaire = $this->repository->findOneBy(['rapport_veterinaire_id'=> $id]);

        if($rapportVeterinaire){
            $code_http= Response::HTTP_OK;
        }
        else{

            $code_http= Response::HTTP_NOT_FOUND;
        }

        return $this->json($rapportVeterinaire,$code_http,['groups'=>'veterinaire_read']);


    }

    #[Route('/{id}', name: 'deleteById',methods: 'DELETE')]
    #[Delete(
        path: "/api/rapportVeterinaire/{id}",
        description: "Suppression rapportVeterinaire",
        summary: "Suppression d'un rapportVeterinaire",

    )]
    #[\OpenApi\Attributes\Response(
        response: "200",
        description: "Le rapportVeterinaire est supprimée!"
    )]
    #[\OpenApi\Attributes\Response(
        response: "404",
        description: "Le rapportVeterinaire n'existe pas!"
    )]
    public function  deleteById(int $id):JsonResponse
    {
        $rapportVeterinaire = $this->repository->findOneBy(['rapport_veterinaire_id'=> $id]);

        if($rapportVeterinaire){

            $this->manager->remove($rapportVeterinaire);
            $this->manager->flush();
            $code_http= Response::HTTP_OK;
        }
        else{
            $code_http= Response::HTTP_NOT_FOUND;
        }

        return $this->json($rapportVeterinaire,$code_http,['groups'=>'veterinaire_read']);
    }

    #[Route('/', name: 'create',methods: 'POST')]
    #[Post(
        path: "/api/rapportVeterinaire/",
        description: "Ajouter un rapport veterinaire.",
        summary: "Ajouter un rapport veterinaire",
        requestBody: new RequestBody(
            content: new JsonContent(
                properties: [
                    new \OpenApi\Attributes\Property(
                        "detail",
                        example: "rapport de l'animal ..."
                    )
                ]
            )
        )
    )]
    #[\OpenApi\Attributes\Response(
        response: "200",
        description: "Le rapport est ajouté !"
    )]
    #[\OpenApi\Attributes\Response(
        response: "409",
        description: "La rapport existe déjà !"
    )]
    public function  create(Request $request):JsonResponse
    {

        $rapportVeterinaire = $this->serializer->deserialize($request->getContent(),RapportVeterinaire::class,'json');

        if($rapportVeterinaire){
            $rapportVeterinaire->setDate(new \DateTimeImmutable());
            $this->manager->persist($rapportVeterinaire);
            $this->manager->flush();
            $code_http= Response::HTTP_CREATED;
        }
        else{
            $code_http= Response::HTTP_BAD_REQUEST;
        }

        return $this->json($rapportVeterinaire,$code_http,['groups'=>'veterinaire_read']);
    }


    #[Route('/{id}', name: 'editById',methods: 'PUT')]
    #[Put(
        path: "/api/rapportVeterinaire/{id}",
        description: "Modifier un rapport veterinaire.",
        summary: "Modifier un rapport veterinaire",
        requestBody: new RequestBody(
            content: new JsonContent(
                properties: [
                    new \OpenApi\Attributes\Property(
                        "detail",
                        example: "rapport de l'animal ..."
                    )
                ]
            )
        )
    )]
    #[\OpenApi\Attributes\Response(
        response: "200",
        description: "Le rapport est modifié !"
    )]
    #[\OpenApi\Attributes\Response(
        response: "409",
        description: "Le rapport n'existe pas !"
    )]
    public function  editById(Request $request,int $id):JsonResponse
    {
        $rapportVeterinaire = $this->repository->findOneBy(['rapport_veterinaire_id'=> $id]);

        $rapportVeterinaire_request = $this->serializer->deserialize($request->getContent(),RapportVeterinaire::class,'json');

        if($rapportVeterinaire){


            $rapportVeterinaire->setDetail($rapportVeterinaire_request->getDetail());
            $this->manager->persist($rapportVeterinaire);
            $this->manager->flush();
            $code_http= Response::HTTP_OK;
        }
        else{

            $code_http= Response::HTTP_BAD_REQUEST;
        }

        return $this->json($rapportVeterinaire,$code_http,['groups'=>'veterinaire_read']);
    }
}
