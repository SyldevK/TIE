<?php

namespace App\Controller\Api;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Validator\Constraints as Assert;

#[AsController]
class RegistrationController
{
    #[Route('/api/register', name: 'api_register', methods: ['POST'])]
    public function register(
        Request $request,
        EntityManagerInterface $entityManager,
        UserPasswordHasherInterface $passwordHasher,
        ValidatorInterface $validator
    ): JsonResponse {
        $data = json_decode($request->getContent(), true);

        $email = $data['email'] ?? null;
        $password = $data['password'] ?? null;

        // Vérification rapide des champs
        if (!$email || !$password) {
            return new JsonResponse(['message' => 'Email and password are required'], 400);
        }

        // Validation de l'email (format correct)
        $violations = $validator->validate($email, [
            new Assert\NotBlank(),
            new Assert\Email()
        ]);

        if (count($violations) > 0) {
            return new JsonResponse(['message' => 'Invalid email address'], 400);
        }

        // Vérification si un utilisateur existe déjà
        $existingUser = $entityManager->getRepository(User::class)->findOneBy(['email' => $email]);
        if ($existingUser) {
            return new JsonResponse(['message' => 'Email already used'], 409); // 409 Conflict
        }

        // Création du nouvel utilisateur
        $user = new User();
        $user->setEmail($email);
        $user->setRoles(['ROLE_USER']);
        $user->setPassword(
            $passwordHasher->hashPassword($user, $password)
        );

        $entityManager->persist($user);
        $entityManager->flush();

        return new JsonResponse(['message' => 'User created successfully'], 201);
    }
}
