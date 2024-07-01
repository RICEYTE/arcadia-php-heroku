<?php

namespace App\Controller;

use App\Entity\service
;
use App\Repository\ServiceRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\SerializerInterface;

#[Route('/api/service',name: 'app_service_')]
class ServiceController extends AbstractController
{
    private $manager;
    private $repository;
    private $serializer;
    public function __construct(
        EntityManagerInterface $manager,
        SerializerInterface $serializer,
        ServiceRepository $repository
    )
    {
        $this->repository = $repository;
        $this->manager = $manager;
        $this->serializer = $serializer;
    }

    #[Route('/',name: 'getAll',methods: 'GET')]
    public function  getAll():JsonResponse
    {
        $servicesList = $this->repository->findAll();

        if($servicesList){
            $data = $this->serializer->serialize($servicesList,'json');
            $code_http= Response::HTTP_OK;
        }
        else{
            $data = $this->serializer->serialize("Aucun service trouvé!",'json');
            $code_http= Response::HTTP_NOT_FOUND;
        }

        $jsonResponse = new JsonResponse($data,$code_http,[],'true');

        return $jsonResponse;
    }

    #[Route('/{id}', name: 'getById',methods: 'GET')]
    public function  getById(int $id):JsonResponse
    {
        $service = $this->repository->findOneBy(['service_id'=> $id]);

        if($service){
            $data = $this->serializer->serialize($service,'json');
            $code_http= Response::HTTP_OK;
        }
        else{
            $data = $this->serializer->serialize("Service $id non trouvé!",'json');
            $code_http= Response::HTTP_NOT_FOUND;
        }

        $jsonResponse = new JsonResponse($data,$code_http,[],'true');

        return $jsonResponse;
    }

    #[Route('/{id}', name: 'deleteById',methods: 'DELETE')]
    public function  deleteById(int $id):JsonResponse
    {
        $service = $this->repository->findOneBy(['service_id'=> $id]);

        if($service){

            $this->manager->remove($service);
            $this->manager->flush();
            $data = $this->serializer->serialize("service $id supprimmé !",'json');
            $code_http= Response::HTTP_OK;
        }
        else{
            $data = $this->serializer->serialize("service $id non trouvé!",'json');
            $code_http= Response::HTTP_NOT_FOUND;
        }

        $jsonResponse = new JsonResponse($data,$code_http,[],'true');

        return $jsonResponse;
    }

    #[Route('/', name: 'create',methods: 'POST')]
    public function  create(Request $request):JsonResponse
    {

        $service = $this->serializer->deserialize($request->getContent(),service::class,'json');

        if($service){

            $this->manager->persist($service);
            $this->manager->flush();
            $data = $this->serializer->serialize($service,'json');
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
        $service = $this->repository->findOneBy(['service_id'=> $id]);

        $service_request = $this->serializer->deserialize($request->getContent(),service::class,'json');

        if($service){

            $service->setNom($service_request->getNom());
            $service->setDescription($service_request->getDescription());
            $service->setCommentaireservice($service_request->getCommentaireservice());
            $this->manager->persist($service);
            $this->manager->flush();
            $data = $this->serializer->serialize($service,'json');
            $code_http= Response::HTTP_OK;
        }
        else{
            $data = $this->serializer->serialize("service $id non trouvé!",'json');
            $code_http= Response::HTTP_BAD_REQUEST;
        }

        $jsonResponse = new JsonResponse($data,$code_http,[],'true');

        return $jsonResponse;
    }
}
