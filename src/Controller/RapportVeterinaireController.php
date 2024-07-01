<?php

namespace App\Controller;

use App\Entity\RapportVeterinaire;
use App\Repository\RapportVeterinaireRepository;
use Doctrine\ORM\EntityManagerInterface;
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
    public function  getAll():JsonResponse
    {
        $rapportVeterinairesList = $this->repository->findAll();

        if($rapportVeterinairesList){
            $data = $this->serializer->serialize($rapportVeterinairesList,'json');
            $code_http= Response::HTTP_OK;
        }
        else{
            $data = $this->serializer->serialize("Aucun rapportVeterinaire trouvé!",'json');
            $code_http= Response::HTTP_NOT_FOUND;
        }

        $jsonResponse = new JsonResponse($data,$code_http,[],'true');

        return $jsonResponse;
    }

    #[Route('/{id}', name: 'getById',methods: 'GET')]
    public function  getById(int $id):JsonResponse
    {
        $rapportVeterinaire = $this->repository->findOneBy(['rapportVeterinaire_id'=> $id]);

        if($rapportVeterinaire){
            $data = $this->serializer->serialize($rapportVeterinaire,'json');
            $code_http= Response::HTTP_OK;
        }
        else{
            $data = $this->serializer->serialize("RapportVeterinaire $id non trouvé!",'json');
            $code_http= Response::HTTP_NOT_FOUND;
        }

        $jsonResponse = new JsonResponse($data,$code_http,[],'true');

        return $jsonResponse;
    }

    #[Route('/{id}', name: 'deleteById',methods: 'DELETE')]
    public function  deleteById(int $id):JsonResponse
    {
        $rapportVeterinaire = $this->repository->findOneBy(['rapportVeterinaire_id'=> $id]);

        if($rapportVeterinaire){

            $this->manager->remove($rapportVeterinaire);
            $this->manager->flush();
            $data = $this->serializer->serialize("RapportVeterinaire $id supprimmé !",'json');
            $code_http= Response::HTTP_OK;
        }
        else{
            $data = $this->serializer->serialize("RapportVeterinaire $id non trouvé!",'json');
            $code_http= Response::HTTP_NOT_FOUND;
        }

        $jsonResponse = new JsonResponse($data,$code_http,[],'true');

        return $jsonResponse;
    }

    #[Route('/', name: 'create',methods: 'POST')]
    public function  create(Request $request):JsonResponse
    {

        $rapportVeterinaire = $this->serializer->deserialize($request->getContent(),RapportVeterinaire::class,'json');

        if($rapportVeterinaire){

            $this->manager->persist($rapportVeterinaire);
            $this->manager->flush();
            $data = $this->serializer->serialize($rapportVeterinaire,'json');
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
        $rapportVeterinaire = $this->repository->findOneBy(['rapportVeterinaire_id'=> $id]);

        $rapportVeterinaire_request = $this->serializer->deserialize($request->getContent(),RapportVeterinaire::class,'json');

        if($rapportVeterinaire){

            $rapportVeterinaire->setNom($rapportVeterinaire_request->getNom());
            $rapportVeterinaire->setDescription($rapportVeterinaire_request->getDescription());
            $rapportVeterinaire->setCommentaireRapportVeterinaire($rapportVeterinaire_request->getCommentaireRapportVeterinaire());
            $this->manager->persist($rapportVeterinaire);
            $this->manager->flush();
            $data = $this->serializer->serialize($rapportVeterinaire,'json');
            $code_http= Response::HTTP_OK;
        }
        else{
            $data = $this->serializer->serialize("RapportVeterinaire $id non trouvé!",'json');
            $code_http= Response::HTTP_BAD_REQUEST;
        }

        $jsonResponse = new JsonResponse($data,$code_http,[],'true');

        return $jsonResponse;
    }
}
