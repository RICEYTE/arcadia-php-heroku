<?php

namespace App\Controller;




use App\Entity\Utilisateur;
use App\Repository\RoleRepository;
use App\Repository\UtilisateurRepository;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\EntityManagerInterface;
use OpenApi\Annotations as OA;
use OpenApi\Attributes\JsonContent;
use OpenApi\Attributes\Parameter;
use OpenApi\Attributes\Post;
use OpenApi\Attributes\RequestBody;
use phpDocumentor\Reflection\DocBlock\Description;
use phpDocumentor\Reflection\DocBlock\Tags\Example;
use phpDocumentor\Reflection\DocBlock\Tags\Property;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;
use Symfony\Component\Serializer\SerializerInterface;

#[Route('/api',name: 'app_api_')]
class SecurityController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $manager,
        private SerializerInterface $serializer,
        private UtilisateurRepository $userRepository,
        private RoleRepository $roleRepository
    ){}


    #[Route('/registration', name: 'registration', methods: 'POST')]
    #[Post(
        path: "/api/registration",
        description: "Inscription d'un nouvel utilisateur",
        summary: "Inscription d'un nouvel utilisateur",
    )]
    #[\OpenApi\Attributes\Parameter(
        parameter: "username",
        name:"username",
        description:"",
        in: "query",
        required: true,
        allowEmptyValue: false)]
    #[\OpenApi\Attributes\Parameter(
        parameter: "password",
        name:"password",
        description:"",
        in: "query",
        required: true,
        allowEmptyValue: false)]
    #[RequestBody(
        description: "TEST",
        request: "Ma request...",
        required: "true",
        content: new JsonContent(
            type: 'object',properties: [],example: 'user@company.fr'
        ))]
    public function register(Request $request, UserPasswordHasherInterface $passwordHasher): JsonResponse
    {


        $user = $this->serializer->deserialize($request->getContent(),Utilisateur::class,'json');
        $search_user = $this->userRepository->findOneBy(['username'=>$user->getUsername()]);

        if ($search_user){
            $data= "Utilisateur déjà enregistré !";
            $code_http = Response::HTTP_BAD_REQUEST;

        }
        else{


            $user->setPassword($passwordHasher->hashPassword($user,$user->getPassword()) );
            $user->setCreatedAt(new \DateTimeImmutable());

            dd($user);
            $this->manager->persist($user);
            $this->manager->flush();

            $data = $user;
            $code_http = Response::HTTP_CREATED;

        }

/*
        return new JsonResponse([
            'user' =>$user->getUserIdentifier(),
            'apiToken' => $user->getApiToken(),
            'roles' => $user->getRoles(),
            'username' =>$user->getUsername()
        ],Response::HTTP_CREATED);
*/
        return $this->json($data,$code_http,[],['groups'=>'user_read']);

    }


    /**
     * @param Utilisateur|null $user
     * @return JsonResponse
     */
    #[Route('/login', name: 'login',methods:'POST')]
    /**
     * @OA\Post(
     *     @OA\Parameter(
     *         name="username",
     *     in="query",
     *     required=true,
     *     parameter="username"
     *     )
     * )
     */
    #[Post(
        path: '/api/login',
        description: "login...",
        summary: 'Methode de connexion',
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
                    )
                ]
            )
        )
    )
    ]
    public function login(#[CurrentUser] ?Utilisateur $user): JsonResponse
    {

        if ($user == null){
            return new JsonResponse([
                'message' => 'missing credentials !'
            ],Response::HTTP_UNAUTHORIZED);
        }

        return new JsonResponse([
            'user' =>$user->getUserIdentifier(),
            'roles' => $user->getRoles()
        ], Response::HTTP_OK);

    }



private function getRole(Request $request, Array $roles): ?Role{

    $request_content = $request->getContent();

    $existRole = false;
    $index=0;
    while($index < count($roles) and !$existRole){
        $role = $roles->get($index);
        $role_name = $role->getLabel();
        $existRole = preg_match("/$role_name/",$request_content,$request_array_role,0,0);

        $index++;
    }
    if($existRole){
        return $role[$index-1];}
    else{
        return null;
    }
}
}