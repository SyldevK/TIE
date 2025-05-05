<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use SymfonyCasts\Bundle\ResetPassword\ResetPasswordHelperInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;

class ResetPasswordController extends AbstractController
{
  private ResetPasswordHelperInterface $resetPasswordHelper;
  private MailerInterface $mailer;
  private EntityManagerInterface $entityManager;

  public function __construct(
    ResetPasswordHelperInterface $resetPasswordHelper,
    MailerInterface $mailer,
    EntityManagerInterface $entityManager
  ) {
    $this->resetPasswordHelper = $resetPasswordHelper;
    $this->mailer = $mailer;
    $this->entityManager = $entityManager;
  }

  #[Route('/api/forgot-password', name: 'api_forgot_password', methods: ['POST'])]
  public function forgotPassword(Request $request, UserRepository $userRepository): JsonResponse
  {
    $data = json_decode($request->getContent(), true);
    $email = $data['email'] ?? '';

    if (!$email) {
      return $this->json(['error' => 'Email requis'], 400);
    }

    $user = $userRepository->findOneBy(['email' => $email]);

    if (!$user) {
      return $this->json(['message' => 'Si cet email existe, un lien a été envoyé.']);
    }

    $resetToken = $this->resetPasswordHelper->generateResetToken($user);

    // 💡 Détection mobile ou web via User-Agent
    $ua = strtolower($request->headers->get('User-Agent', ''));
    $isMobile = str_contains($ua, 'android') || str_contains($ua, 'iphone');

    $token = $resetToken->getToken();

    $link = $isMobile
      ? "troupedesechappees://reset-password?token=$token"
      : "http://tie.test/index.html#/reset-password?token=$token";

    $html = "
      <p>Bonjour,</p>
      <p>Vous avez demandé à réinitialiser votre mot de passe pour accéder à votre compte La Troupe des Échappées.</p>
      <p>Veuillez cliquer sur le bouton ci-dessous :</p>
      <p>
          <a href=\"$link\" 
          style=\"background-color:#6A0DAD;color:white;padding:12px 24px;text-decoration:none;border-radius:8px;font-family:Poppins;font-size:16px;\">
          Réinitialiser mon mot de passe
          </a>
      </p>
      <p>Si vous n'avez pas demandé cette réinitialisation, ignorez simplement cet e-mail.</p>
      <p>À très bientôt !<br>La Troupe des Échappées 🎭</p>
    ";

    $emailMessage = (new Email())
      ->from('noreply@latroupedesechappees.fr')
      ->to($user->getEmail())
      ->subject('Réinitialisation de votre mot de passe')
      ->html($html);

    $this->mailer->send($emailMessage);

    return $this->json(['message' => 'Si cet email existe, un lien a été envoyé.']);
  }

  #[Route('/api/reset-password', name: 'api_reset_password', methods: ['POST'])]
  public function resetPassword(Request $request, UserRepository $userRepository): JsonResponse
  {
    $data = json_decode($request->getContent(), true);
    $token = $data['token'] ?? '';
    $newPassword = $data['password'] ?? '';

    if (!$token || !$newPassword) {
      return $this->json(['error' => 'Token et nouveau mot de passe requis.'], 400);
    }

    try {
      $user = $this->resetPasswordHelper->validateTokenAndFetchUser($token);
    } catch (\Exception $e) {
      return $this->json(['error' => 'Token invalide ou expiré.'], 400);
    }

    $user->setPassword(
      password_hash($newPassword, PASSWORD_BCRYPT)
    );

    $this->entityManager->flush();
    $this->resetPasswordHelper->removeResetRequest($token);

    // 🟣 Mail de confirmation
    $confirmationEmail = (new Email())
      ->from('noreply@latroupedesechappees.fr')
      ->to($user->getEmail())
      ->subject('Votre mot de passe a été changé')
      ->html('
        <p>Bonjour,</p>
        <p>Votre mot de passe a été modifié avec succès pour votre compte La Troupe des Échappées.</p>
        <p>Si vous n\'êtes pas à l\'origine de ce changement, veuillez nous contacter immédiatement.</p>
        <p>À très bientôt !<br>La Troupe des Échappées 🎭</p>
      ');

    $this->mailer->send($confirmationEmail);

    return $this->json(['message' => 'Mot de passe réinitialisé avec succès.']);
  }
}
