<?php

namespace App\Controller;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Mailer\MailerInterface;
use Twig\Environment;
use Symfony\Component\Mime\Email;

#[AsController]
class RegistrationController
{
    #[Route('/api/register', name: 'api_register', methods: ['POST'])]
    public function register(
        Request $request,
        EntityManagerInterface $entityManager,
        UserPasswordHasherInterface $passwordHasher,
        ValidatorInterface $validator,
        MailerInterface $mailer,
        Environment $twig
    ): JsonResponse {
        $data = json_decode($request->getContent(), true);

        $email = $data['email'] ?? null;
        $password = $data['password'] ?? null;
        $nom = $data['nom'] ?? null;
        $prenom = $data['prenom'] ?? null;

        if (!$email || !$password || !$nom || !$prenom) {
            return new JsonResponse(['message' => 'Tous les champs sont requis'], 400);
        }

        $violations = $validator->validate($email, [
            new Assert\NotBlank(),
            new Assert\Email(),
        ]);

        if (count($violations) > 0) {
            return new JsonResponse(['message' => 'Adresse e-mail invalide'], 400);
        }

        $existingUser = $entityManager->getRepository(User::class)->findOneBy(['email' => $email]);
        if ($existingUser) {
            return new JsonResponse(['message' => 'Email déjà utilisé'], 409);
        }

        // ✅ Création du nouvel utilisateur
        $user = new User();
        $user->setEmail($email);
        $user->setRoles(['ROLE_USER']);
        $user->setNom($nom);
        $user->setPrenom($prenom);
        $user->setIsVerified(false);

        // ✅ Génération du token de vérification
        $token = bin2hex(random_bytes(32));
        $user->setVerificationToken($token);

        $user->setPassword(
            $passwordHasher->hashPassword($user, $password)
        );

        $entityManager->persist($user);
        $entityManager->flush();

        // ✅ Lien de vérification
        $verificationLink = 'http://tie.test/verify-email?token=' . $token;

        // ✅ Envoi de l’e-mail HTML
        $html = $twig->render('emails/confirmation_email.html.twig', [
            'prenom' => $user->getPrenom(),
            'link' => $verificationLink,
        ]);

        $emailMessage = (new Email())
            ->from('no-reply@tie.test')
            ->to($user->getEmail())
            ->subject('Confirmez votre adresse e-mail')
            ->html($html);

        $mailer->send($emailMessage);

        return new JsonResponse(['message' => 'Utilisateur créé avec succès'], 201);
    }
}
