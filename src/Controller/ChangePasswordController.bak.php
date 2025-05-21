<?php

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class ChangePasswordController extends AbstractController
{
    #[Route('/api/change-password', name: 'api_change_password', methods: ['POST'])]
    #[IsGranted('IS_AUTHENTICATED_FULLY')]
    public function changePassword(
        Request $request,
        User $user,
        UserPasswordHasherInterface $passwordHasher,
        EntityManagerInterface $em
    ): JsonResponse {
        $data = json_decode($request->getContent(), true);
        $ancien = $data['ancien'] ?? null;
        $nouveau = $data['nouveau'] ?? null;

        if (!$ancien || !$nouveau) {
            return new JsonResponse(['error' => 'Champs manquants'], 400);
        }

        if (!$passwordHasher->isPasswordValid($user, $ancien)) {
            return new JsonResponse(['error' => 'Ancien mot de passe incorrect'], 403);
        }

        $user->setPassword($passwordHasher->hashPassword($user, $nouveau));
        $em->flush();

        return new JsonResponse(['message' => 'Mot de passe modifié avec succès']);
    }
}
