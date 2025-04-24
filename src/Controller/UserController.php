<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class UserController extends AbstractController
{

    #[Route('/api/me', name: 'api_me', methods: ['GET'])]
    #[IsGranted('ROLE_USER')]
    public function me(SerializerInterface $serializer, UserInterface $user): JsonResponse
    {

        $data = $serializer->serialize($user, 'json', ['groups' => ['user:read']]);
        return new JsonResponse($data, 200, [], true);
    }
}
