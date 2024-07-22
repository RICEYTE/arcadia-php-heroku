<?php

namespace App\Controller;

use App\Entity\Role;
use App\Repository\RoleRepository;
use Doctrine\ORM\EntityManagerInterface;
use OpenApi\Attributes\Delete;
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

#[Route('/api/role',name: 'app_role_')]
class RoleController extends AbstractController
{

    private $manager;
    private $repository;
    private $serializer;

    public function __construct(
        EntityManagerInterface $manager,
        RoleRepository $repository,
        SerializerInterface $serializer,
    )

    {
        $this->repository = $repository;
        $this->manager = $manager;
        $this->serializer = $serializer;
    }

    #[Get(
        path: "/api/role/",
        description: "Récupération de la liste des roles",
        summary: "Récupérer la liste des roles",

    )]
    #[\OpenApi\Attributes\Response(
        response: "200",
        description: "Les roles sont affichés !"
    )]
    #[Route('/',name: 'showAll',methods: 'GET')]
    public function  getAll():JsonResponse
    {
        return $this->json($this->repository->findAll(),200,['groups'=>'role_read']);
    }

    #[Get(
        path: "/api/role/{label}",
        description: "Récupération du role à partir de son label",
        summary: "Affichage du role à partir de son label",

    )]
    #[\OpenApi\Attributes\Response(
        response: "200",
        description: "Le role à été trouvé et affiché !"
    )]
    #[Route('/{label}', name: 'getByLabel',methods: 'GET')]
    public function  getById(string $label):JsonResponse
    {
        $role = $this->repository->findOneBy(['label'=> $label]);

        if($role){
            $code_http= Response::HTTP_OK;
        }
        else{
            $code_http= Response::HTTP_NOT_FOUND;
        }

        return $this->json($role,$code_http,['groups'=>'role_read']);

    }

    #[Route('/{label}', name: 'deleteByLabel',methods: 'DELETE')]
    #[Delete(
        path: "/api/role/{label}",
        description: "Suppression à partir de son label",
        summary: "Suppression d'un role à partir de son label",

    )]
    #[\OpenApi\Attributes\Response(
        response: "200",
        description: "Le role à été supprimé!"
    )]
    public function  deleteByLabel(string $label):JsonResponse
    {
        $role = $this->repository->findOneBy(['label'=> $label]);

        if($role){

            $this->manager->remove($role);
            $this->manager->flush();
            $code_http= Response::HTTP_OK;
        }
        else{
            $code_http= Response::HTTP_NOT_FOUND;
        }

        return $this->json($role,$code_http,['groups'=>'role_read']);


    }

    #[Post(
        path: "/api/role/",
        description: "Ajouter un role utilisateur.",
        summary: "Ajouter un role utilisateur",
        requestBody: new RequestBody(
            content: new JsonContent(
                properties: [
                    new \OpenApi\Attributes\Property(
                        "label",
                        example: "ROLE_EMPLOYE"
                    )
                ]
            )
        )
    )]
    #[\OpenApi\Attributes\Response(
        response: "200",
        description: "Le role est ajouté !"
    )]
    #[Route('/', name: 'create',methods: 'POST')]
    public function  create(Request $request):JsonResponse
    {

        $role = $this->serializer->deserialize($request->getContent(),Role::class,'json');

        if($role){

            $this->manager->persist($role);
            $this->manager->flush();
            $code_http= Response::HTTP_CREATED;
        }
        else{
            $code_http= Response::HTTP_BAD_REQUEST;
        }

        return $this->json($role,$code_http,['groups'=>'role_read']);
    }


    #[Route('/label}', name: 'editByLabel',methods: 'PUT')]
    public function  editByLabel(Request $request,string $label):JsonResponse
    {
        $role = $this->repository->findOneBy(['label'=> $label]);

        $role_request = $this->serializer->deserialize($request->getContent(),Role::class,'json');

        if($role){

            $role->setNom($role_request->getNom());
            $role->setDescription($role_request->getDescription());
            $role->setCommentaireRole($role_request->getCommentaireRole());
            $this->manager->persist($role);
            $this->manager->flush();
            $code_http= Response::HTTP_OK;
        }
        else{
            $code_http= Response::HTTP_BAD_REQUEST;
        }

        $jsonResponse = new JsonResponse($data,$code_http,[],'true');

        return $jsonResponse;
    }
}
