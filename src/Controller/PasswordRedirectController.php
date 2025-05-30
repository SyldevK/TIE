<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\Annotation\Route;

class PasswordRedirectController
{
    #[Route('/reset-password-link', name: 'redirect_reset_password')]
    public function redirectResetPassword(Request $request): RedirectResponse
    {
        $token = $request->query->get('token');

        if (!$token) {
            return new RedirectResponse('http://tie.test/index.html#/page-lien-invalide');
        }

        // Détection mobile simple via User-Agent
        $ua = strtolower($request->headers->get('User-Agent', ''));
        $isMobile = str_contains($ua, 'android') || str_contains($ua, 'iphone');

        if ($isMobile) {
            return new RedirectResponse("troupedesechappees://reset-password?token=$token");
        }

        return new RedirectResponse("http://tie.test/index.html#/reset-password?token=$token");
    }

    #[Route('/page-lien-invalide', name: 'invalid_link')]
    public function fallback(): Response
    {
        return new Response('<h1>Ce lien doit être ouvert depuis un téléphone avec l’application installée.</h1>');
    }
}
