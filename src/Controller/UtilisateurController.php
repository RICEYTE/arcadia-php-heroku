<?php

namespace App\Controller;

use App\Entity\Utilisateur;
use App\Repository\UtilisateurRepository;
use Doctrine\ORM\EntityManagerInterface;
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
    public function  getById(string $username):JsonResponse
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
    #[Route('/{id}', name: 'deleteById',methods: 'DELETE')]
    public function  deleteById(int $id):JsonResponse
    {
        $utilisateur = $this->repository->findOneBy(['utilisateur_id'=> $id]);

        if($utilisateur){

            $this->manager->remove($utilisateur);
            $this->manager->flush();
            $data = $this->serializer->serialize("utilisateur $id supprimmé !",'json');
            $code_http= Response::HTTP_OK;
        }
        else{
            $data = $this->serializer->serialize("utilisateur $id non trouvé!",'json');
            $code_http= Response::HTTP_NOT_FOUND;
        }

        $jsonResponse = new JsonResponse($data,$code_http,[],'true');

        return $jsonResponse;
    }

    #[Route('/', name: 'create',methods: 'POST')]
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


    #[Route('/{id}', name: 'editById',methods: 'PUT')]
    public function  editById(Request $request,int $id):JsonResponse
    {
        $utilisateur = $this->repository->findOneBy(['utilisateur_id'=> $id]);

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
            $data = $this->serializer->serialize("utilisateur $id non trouvé!",'json');
            $code_http= Response::HTTP_BAD_REQUEST;
        }

        $jsonResponse = new JsonResponse($data,$code_http,[],'true');

        return $jsonResponse;
    }
}
