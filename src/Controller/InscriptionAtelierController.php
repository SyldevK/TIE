<?php

namespace App\Controller;

use App\Entity\Participant;
use App\Entity\Enrollment;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Twig\Environment;

class InscriptionAtelierController
{
    private EntityManagerInterface $em;
    private MailerInterface $mailer;
    private Environment $twig;
    private UserRepository $userRepository;

    public function __construct(
        EntityManagerInterface $em,
        MailerInterface $mailer,
        Environment $twig,
        UserRepository $userRepository
    ) {
        $this->em = $em;
        $this->mailer = $mailer;
        $this->twig = $twig;
        $this->userRepository = $userRepository;
    }

    #[Route('/api/inscription-atelier', name: 'inscription_atelier', methods: ['POST'])]
    public function __invoke(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        $nom = $data['nom'] ?? '';
        $prenom = $data['prenom'] ?? '';
        $email = $data['email'] ?? '';
        $atelier = $data['atelier'] ?? '';

        if (!$nom || !$prenom || !$email || !$atelier) {
            return new JsonResponse(['error' => 'Champs manquants'], 400);
        }

        // 🔐 Étape 1 – Récupérer un User fictif ou "anonyme"
        $user = $this->userRepository->findOneByEmail($email);

        if (!$user) {
            return new JsonResponse(['error' => 'Utilisateur non trouvé. Veuillez vous connecter.'], 401);
        }

        // 👶 Étape 2 – Créer le participant
        $participant = new Participant();
        $participant->setNom($nom);
        $participant->setPrenom($prenom);

        $this->em->persist($participant);

        // 📝 Étape 3 – Créer l’enrollment
        $enrollment = new Enrollment();
        $enrollment->setUser($user);
        $enrollment->setParticipant($participant);
        $enrollment->setGroupe($atelier);
        $enrollment->setIsActive(false); // à activer manuellement ?
        $enrollment->setAnneeScolaire(date('Y'));

        $this->em->persist($enrollment);
        $this->em->flush();

        // 📬 Étape 4 – Envoyer l’email
        $html = $this->twig->render('emails/enrollment_notification.html.twig', [
            'participant' => "$prenom $nom",
            'user' => $user->getNom() . ' ' . $user->getPrenom(),
            'groupe' => $atelier,
            'annee' => date('Y'),
        ]);

        try {
            $emailObj = (new Email())
                ->from('noreply@latroupedesechappees.fr')
                ->to('alyomeguja@gmail.com')
                ->subject('Nouvelle inscription à un atelier')
                ->html($html);

            $this->mailer->send($emailObj);
        } catch (\Throwable $e) {
            error_log('Erreur email : ' . $e->getMessage());
        }

        return new JsonResponse(['message' => 'Inscription enregistrée et email envoyé.']);
    }
}
