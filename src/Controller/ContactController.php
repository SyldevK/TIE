<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\ContactMessage;

class ContactController extends AbstractController
{
    #[Route('/api/contact', name: 'api_contact', methods: ['POST'])]
    public function contact(Request $request, MailerInterface $mailer, EntityManagerInterface $em): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        if (empty($data['nom']) || empty($data['prenom']) || empty($data['email']) || empty($data['message'])) {
            return $this->json(['error' => 'Tous les champs sont obligatoires'], 400);
        }

        $contactMessage = new ContactMessage();
        $contactMessage->setNom($data['nom']);
        $contactMessage->setPrenom($data['prenom']);
        $contactMessage->setEmail($data['email']);
        $contactMessage->setMessage($data['message']);
        $em->persist($contactMessage);
        $em->flush();

        $email = (new Email())
            ->from('latroupedesechappees@gmail.com')
            ->replyTo($data['email'])
            ->to('ayomeguja@gmail.com')
            ->subject('Nouveau message de contact')
            ->text(
                "Nom: {$data['nom']}\n" .
                    "Prénom: {$data['prenom']}\n" .
                    "Email: {$data['email']}\n\n" .
                    "Message:\n{$data['message']}"
            );

        try {
            $mailer->send($email);
        } catch (\Exception $e) {
            return $this->json([
                'error' => 'Erreur lors de l\'envoi du mail',
                'exception' => $e->getMessage()
            ], 500);
        }


        return $this->json(['success' => 'Message envoyé'], 200);
    }
}
