<?php

namespace App\Controller;

use App\Entity\Utilisateur;
use App\Repository\UtilisateurRepository;
use Doctrine\ORM\EntityManagerInterface;
use OpenApi\Attributes\Delete;
use OpenApi\Attributes\Get;
use OpenApi\Attributes\JsonContent;
use OpenApi\Attributes\Post;
use OpenApi\Attributes\Put;
use OpenApi\Attributes\RequestBody;
use phpDocumentor\Reflection\DocBlock\Tags\Property;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\SerializerInterface;

#[Route('/api/utilisateur',name: 'app_utilisateur_')]
class UtilisateurController extends AbstractController
{

    private $manager;
    private $repository;
    private $serializer;

    public function __construct(
        EntityManagerInterface $manager,
        UtilisateurRepository $repository,
        SerializerInterface $serializer,
    )

    {
        $this->repository = $repository;
        $this->manager = $manager;
        $this->serializer = $serializer;
    }

    #[Route('/',name: 'showAll',methods: 'GET')]
    #[Get(
        path: '/api/utilisateur/',
        description: "Récupération de la liste de tous les utilisateurs du site",
        summary: 'Recherche de toous les utilisateurs.',
    )]
    #[\OpenApi\Attributes\Response(
        response: "200",
        description: "Utilisateur(s) trouvé(s)"
    )]
    #[\OpenApi\Attributes\Response(
        response: "404",
        description: "Utilisateurs non trouvés."
    )]
    public function  getAll():JsonResponse
    {
        $utilisateursList = $this->repository->findAll();

        foreach ($utilisateursList as $utilisateur){
            $utilisateur->setPassword("");

        }

        if($utilisateursList){
            $data = $this->serializer->serialize($utilisateursList,'json');
            $code_http= Response::HTTP_OK;
        }
        else{
            $data = $this->serializer->serialize("Aucun utilisateur trouvé!",'json');
            $code_http= Response::HTTP_NOT_FOUND;
        }

        $jsonResponse = new JsonResponse($data,$code_http,[],'true');

        return $jsonResponse;
    }

    #[Route('/{username}', name: 'getByUsername',methods: 'GET')]
    #[Get(
        path: '/api/utilisateur/{username}',
        description: "Recherche d'un utilisateur par son username. Entrer le username de l'utilisateur",
        summary: 'Recherche d\'un utilisateur par son username',
    )]
    #[\OpenApi\Attributes\Response(
        response: "200",
        description: "Utilisateur trouvé"
    )]
    #[\OpenApi\Attributes\Response(
        response: "404",
        description: "Utilisateur non trouvé."
    )]
    public function  getByUsername(string $username):JsonResponse
    {
        $utilisateur = $this->repository->findOneBy(['username'=> $username]);

        if($utilisateur){
           // $data = $this->serializer->serialize($utilisateur,'json');
            $data =$this->serializer->serialize( [
                'nom' =>$utilisateur->getNom(),
                'prenom' => $utilisateur->getPrenom(),
                'username' =>$utilisateur->getUsername(),
                'roles' => $utilisateur->getRoles(),
            ],'json');

            $code_http= Response::HTTP_OK;
        }
        else{
            $data = $this->serializer->serialize("utilisateur $username non trouvé!",'json');
            $code_http= Response::HTTP_NOT_FOUND;
        }

        $jsonResponse = new JsonResponse($data,$code_http,[],'true');

        return $jsonResponse;
    }
    #[Route('/{username}', name: 'deleteByUsername',methods: 'DELETE')]
    #[Delete(
        path: '/api/utilisateur/{username}',
        description: "Suppression d'un utilisateur par son username. Entrer le username de l'utilisateur",
        summary: 'Suppression d\'un utilisateur par son username',
    )]
    #[\OpenApi\Attributes\Response(
        response: "200",
        description: "Utilisateur supprimé"
    )]
    #[\OpenApi\Attributes\Response(
        response: "404",
        description: "Utilisateur non trouvé."
    )]
    public function  deleteByUsername(string $username):JsonResponse
    {
        $utilisateur = $this->repository->findOneBy(['username'=> $username]);

        if($utilisateur){

            $this->manager->remove($utilisateur);
            $this->manager->flush();
            $data = $this->serializer->serialize("utilisateur $username supprimmé !",'json');
            $code_http= Response::HTTP_OK;
        }
        else{
            $data = $this->serializer->serialize("utilisateur $username non trouvé!",'json');
            $code_http= Response::HTTP_NOT_FOUND;
        }

        $jsonResponse = new JsonResponse($data,$code_http,[],'true');

        return $jsonResponse;
    }

    public function  create(Request $request):JsonResponse
    {

        $utilisateur = $this->serializer->deserialize($request->getContent(),Utilisateur::class,'json');

        if($utilisateur){

            $utilisateur->setCreatedAt(new \DateTimeImmutable());
            $this->manager->persist($utilisateur);
            $this->manager->flush();
            $data = $this->serializer->serialize($utilisateur,'json');
            $code_http= Response::HTTP_CREATED;
        }
        else{
            $data = $this->serializer->serialize("Création impossible!",'json');
            $code_http= Response::HTTP_BAD_REQUEST;
        }

        $jsonResponse = new JsonResponse($data,$code_http,[],'true');

        return $jsonResponse;
    }


    #[Route('/{username}', name: 'editByUsername',methods: 'PUT')]
    #[Put(
        path: '/api/utilisateur/{username}',
        description: "Modification d'un utilisateur par son username. Entrer le username de l'utilisateur",
        summary: 'Modification d\'un utilisateur par son username',
        requestBody: new RequestBody(
            content: new JsonContent(
                properties: [
                    new \OpenApi\Attributes\Property(
                        "username",
                        example: "user@arcadia.fr"
                    ),
                    new \OpenApi\Attributes\Property(
                        "password",
                        example: "efzf243DFD"
                    ),
                    new \OpenApi\Attributes\Property(
                        "nom",
                        example: "DOE"
                    ),
                    new \OpenApi\Attributes\Property(
                        "prenom",
                        example: "Jhon"
                    )
                ]
            )
        )
    )]
    #[\OpenApi\Attributes\Response(
        response: "200",
        description: "Utilisateur modifié"
    )]
    #[\OpenApi\Attributes\Response(
        response: "404",
        description: "Utilisateur non trouvé."
    )]
    #[\OpenApi\Attributes\Response(
        response: "400",
        description: "La requête n'est pas correcte."
    )]
    public function  editByUsername(Request $request,string $username):JsonResponse
    {
        $utilisateur = $this->repository->findOneBy(['username'=> $username
]);

        $utilisateur_request = $this->serializer->deserialize($request->getContent(),Utilisateur::class,'json');

        if($utilisateur){

            $utilisateur->setNom($utilisateur_request->getNom());
            $utilisateur->setDescription($utilisateur_request->getDescription());
            $utilisateur->setCommentaireutilisateur($utilisateur_request->getCommentaireutilisateur());
            $this->manager->persist($utilisateur);
            $this->manager->flush();
            $data = $this->serializer->serialize($utilisateur,'json');
            $code_http= Response::HTTP_OK;
        }
        else{
            $data = $this->serializer->serialize("utilisateur $username
 non trouvé!",'json');
            $code_http= Response::HTTP_BAD_REQUEST;
        }

        $jsonResponse = new JsonResponse($data,$code_http,[],'true');

        return $jsonResponse;
    }
}
