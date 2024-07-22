<?php

namespace App\Controller;

use App\Entity\Avis;
use App\Repository\AvisRepository;
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

#[Route('/api/avis',name: 'app_avis_')]
class AvisController extends AbstractController
{

    private $manager;
    private $repository;
    private $serializer;

    public function __construct(
        EntityManagerInterface $manager,
        AvisRepository $repository,
        SerializerInterface $serializer,
    )

    {
        $this->repository = $repository;
        $this->manager = $manager;
        $this->serializer = $serializer;
    }

    #[Route('/',name: 'showAll',methods: 'GET')]
    #[Get(
        path: "/api/avis/",
        description: "Afficher les avis",
        summary: "Afficher les avis"
    )]
    #[\OpenApi\Attributes\Response(
        response: "200",
        description: "La liste des avis est affichée !"
    )]
    public function  getAll():JsonResponse
    {
        return $this->json($this->repository->findAll(),200,[],['groups'=>'avis_read']);
    }

    #[Route('/{id}', name: 'getById',methods: 'GET')]
    #[Get(
        path: "/api/avis/{id}",
        description: "Afficher un avis par son ID",
        summary: "Afficher un avis par son ID"
    )]
    #[\OpenApi\Attributes\Response(
        response: "200",
        description: "L'avis est affiché !"
    )]
    public function  getById(int $id):JsonResponse
    {
        $avis = $this->repository->findOneBy(['avis_id'=> $id]);

        if($avis){
            $code_http= Response::HTTP_OK;
        }
        else{
            $code_http= Response::HTTP_NOT_FOUND;
        }

        return $this->json($avis,$code_http,[],['groups'=>'avis_read']);
    }

    #[Route('/{id}', name: 'deleteById',methods: 'DELETE')]
    #[Delete(
        path: "/api/avis/{id}",
        description: "suppression d'un avis par son ID",
        summary: "suppression d'un avis par son ID"
    )]
    #[\OpenApi\Attributes\Response(
        response: "200",
        description: "L'avis est supprimé !"
    )]
    #[\OpenApi\Attributes\Response(
        response: "404",
        description: "L'avis est introuvable !"
    )]
    public function  deleteById(int $id):JsonResponse
    {
        $avis = $this->repository->findOneBy(['avis_id'=> $id]);

        if($avis){

            $this->manager->remove($avis);
            $this->manager->flush();
            $code_http= Response::HTTP_OK;
        }
        else{
            $code_http= Response::HTTP_NOT_FOUND;
        }
        return $this->json($avis,$code_http,[],['groups'=>'avis_read']);
    }

    #[Route('/', name: 'create',methods: 'POST')]
    #[Post(
        path: "/api/avis/",
        description: "Ajouter un avis.",
        summary: "Ajouter un avis",
        requestBody: new RequestBody(
            content: new JsonContent(
                properties: [
                    new \OpenApi\Attributes\Property(
                        "pseudo",
                        example: "LeLynxDu38"
                    ),
                    new \OpenApi\Attributes\Property(
                        "commentaire",
                        example: "Ce Zoo est magnifique !"
                    )
                ]
            )
        )
    )]
    #[\OpenApi\Attributes\Response(
        response: "200",
        description: "L'avis est créé !"
    )]
    public function  create(Request $request):JsonResponse
    {

        $avis = $this->serializer->deserialize($request->getContent(),Avis::class,'json');

        if($avis){
            $avis->setVisible(false);
            $avis->setCreatedAt(new \DateTimeImmutable());
            $this->manager->persist($avis);
            $this->manager->flush();
            $code_http= Response::HTTP_CREATED;
        }
        else{
            $code_http= Response::HTTP_BAD_REQUEST;
        }

        return $this->json($avis,$code_http,[],['groups'=>'avis_read']);
    }


    #[Route('/{id}', name: 'editById',methods: 'PUT')]
    #[Put(
        path: "/api/avis/{id}",
        description: "Modifier un avis.",
        summary: "Modifier un avis",
        requestBody: new RequestBody(
            content: new JsonContent(
                properties: [
                    new \OpenApi\Attributes\Property(
                        "isVisible",
                        example: "true"
                    )
                ]
            )
        )
    )]
    #[\OpenApi\Attributes\Response(
        response: "200",
        description: "L'avis est modifié!"
    )]
    #[\OpenApi\Attributes\Response(
        response: "404",
        description: "L'avis est introuvable!"
    )]
    public function  editById(Request $request,int $id):JsonResponse
    {
        $avis = $this->repository->findOneBy(['avis_id'=> $id]);

        $avis_request = $this->serializer->deserialize($request->getContent(),Avis::class,'json');

        if($avis){

            $avis->setVisible($avis_request->isVisible());
            $this->manager->persist($avis);
            $this->manager->flush();
            $code_http= Response::HTTP_OK;
        }
        else{
            $code_http= Response::HTTP_BAD_REQUEST;
        }

        return $this->json($avis,$code_http,[],['groups'=>'avis_read']);
    }
}
