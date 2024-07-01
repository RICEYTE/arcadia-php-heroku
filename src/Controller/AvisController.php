<?php

namespace App\Controller;

use App\Entity\Avis;
use App\Repository\AvisRepository;
use Doctrine\ORM\EntityManagerInterface;
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
    public function  getAll():JsonResponse
    {
        $avissList = $this->repository->findAll();

        if($avissList){
            $data = $this->serializer->serialize($avissList,'json');
            $code_http= Response::HTTP_OK;
        }
        else{
            $data = $this->serializer->serialize("Aucun avis trouvé!",'json');
            $code_http= Response::HTTP_NOT_FOUND;
        }

        $jsonResponse = new JsonResponse($data,$code_http,[],'true');

        return $jsonResponse;
    }

    #[Route('/{id}', name: 'getById',methods: 'GET')]
    public function  getById(int $id):JsonResponse
    {
        $avis = $this->repository->findOneBy(['avis_id'=> $id]);

        if($avis){
            $data = $this->serializer->serialize($avis,'json');
            $code_http= Response::HTTP_OK;
        }
        else{
            $data = $this->serializer->serialize("Avis $id non trouvé!",'json');
            $code_http= Response::HTTP_NOT_FOUND;
        }

        $jsonResponse = new JsonResponse($data,$code_http,[],'true');

        return $jsonResponse;
    }

    #[Route('/{id}', name: 'deleteById',methods: 'DELETE')]
    public function  deleteById(int $id):JsonResponse
    {
        $avis = $this->repository->findOneBy(['avis_id'=> $id]);

        if($avis){

            $this->manager->remove($avis);
            $this->manager->flush();
            $data = $this->serializer->serialize("Avis $id supprimmé !",'json');
            $code_http= Response::HTTP_OK;
        }
        else{
            $data = $this->serializer->serialize("Avis $id non trouvé!",'json');
            $code_http= Response::HTTP_NOT_FOUND;
        }

        $jsonResponse = new JsonResponse($data,$code_http,[],'true');

        return $jsonResponse;
    }

    #[Route('/', name: 'create',methods: 'POST')]
    public function  create(Request $request):JsonResponse
    {

        $avis = $this->serializer->deserialize($request->getContent(),Avis::class,'json');

        if($avis){

            $this->manager->persist($avis);
            $this->manager->flush();
            $data = $this->serializer->serialize($avis,'json');
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
        $avis = $this->repository->findOneBy(['avis_id'=> $id]);

        $avis_request = $this->serializer->deserialize($request->getContent(),Avis::class,'json');

        if($avis){

            $avis->setNom($avis_request->getNom());
            $avis->setDescription($avis_request->getDescription());
            $avis->setCommentaireAvis($avis_request->getCommentaireAvis());
            $this->manager->persist($avis);
            $this->manager->flush();
            $data = $this->serializer->serialize($avis,'json');
            $code_http= Response::HTTP_OK;
        }
        else{
            $data = $this->serializer->serialize("Avis $id non trouvé!",'json');
            $code_http= Response::HTTP_BAD_REQUEST;
        }

        $jsonResponse = new JsonResponse($data,$code_http,[],'true');

        return $jsonResponse;
    }
}
