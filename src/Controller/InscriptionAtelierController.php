<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Twig\Environment;

class InscriptionAtelierController
{
    #[Route('/api/inscription-atelier', name: 'inscription_atelier', methods: ['POST'])]
    public function __invoke(Request $request, MailerInterface $mailer, Environment $twig): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        $nom = $data['nom'] ?? '';
        $prenom = $data['prenom'] ?? '';
        $email = $data['email'] ?? '';
        $atelier = $data['atelier'] ?? '';

        if (!$nom || !$prenom || !$email || !$atelier) {
            return new JsonResponse(['error' => 'Champs manquants'], 400);
        }

        $html = $twig->render('emails/enrollment_notification.html.twig', [
            'participant' => "$prenom $nom",
            'user' => "$prenom $nom",
            'groupe' => $atelier,
            'annee' => date('Y'), // ou autre valeur par défaut
        ]);

        try {
            $message = (new Email())
                ->from('noreply@tonsite.fr')
                ->to('latroupedesechappees@gmail.com')
                ->subject('Nouvelle inscription à un atelier')
                ->html($html);

            $mailer->send($message);
        } catch (\Throwable $e) {
            error_log("Erreur envoi mail : " . $e->getMessage());
        }

        return new JsonResponse(['message' => 'Demande envoyée']);
    }
}
