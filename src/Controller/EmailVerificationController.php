<?php

namespace App\Controller;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\Annotation\Route;

class EmailVerificationController
{
    #[Route('/verify-email', name: 'verify_email')]
    public function verifyEmail(Request $request, EntityManagerInterface $entityManager): RedirectResponse
    {
        $token = $request->query->get('token');

        if (!$token) {
            return new RedirectResponse('http://tie.test/#/erreur-verification');
        }

        $user = $entityManager->getRepository(User::class)->findOneBy(['verificationToken' => $token]);

        if (!$user) {
            return new RedirectResponse('http://tie.test/#/erreur-verification');
        }

        $user->setIsVerified(true);
        $user->setVerificationToken(null);
        $entityManager->flush();

        // Détection mobile simple via User-Agent
        $ua = strtolower($request->headers->get('User-Agent', ''));
        $isMobile = str_contains($ua, 'android') || str_contains($ua, 'iphone');

        if ($isMobile) {
            return new RedirectResponse('troupedesechappees://verification-ok');
        }

        return new RedirectResponse('troupedesechappees://verification-ok');
    }
}
