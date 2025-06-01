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
use Symfony\Bundle\SecurityBundle\Security;

class InscriptionAtelierController
{
    private EntityManagerInterface $em;
    private MailerInterface $mailer;
    private Environment $twig;
    private Security $security;

    public function __construct(
        EntityManagerInterface $em,
        MailerInterface $mailer,
        Environment $twig,
        Security $security
    ) {
        $this->em = $em;
        $this->mailer = $mailer;
        $this->twig = $twig;
        $this->security = $security;
    }

    #[Route('/api/inscription-atelier', name: 'inscription_atelier', methods: ['POST'])]
    public function __invoke(Request $request): JsonResponse
    {
        /** @var \App\Entity\User $user */
        $user = $this->security->getUser();

        if (!$user || !in_array('ROLE_USER', $user->getRoles())) {
            return new JsonResponse(['error' => 'Accès interdit. Veuillez vous connecter.'], 403);
        }

        // Récupération des données envoyées depuis Flutter/Postman
        $data = json_decode($request->getContent(), true);

        $nom = $data['nom'] ?? '';
        $prenom = $data['prenom'] ?? '';
        $dateNaissanceStr = $data['date_naissance'] ?? null;
        $atelier = $data['atelier'] ?? '';

        // Validation des champs obligatoires
        if (!$nom || !$prenom || !$dateNaissanceStr || !$atelier) {
            return new JsonResponse(['error' => 'Champs manquants'], 400);
        }

        // Conversion de la date de naissance au format DateTime
        $dateNaissance = \DateTime::createFromFormat('d/m/Y', $dateNaissanceStr);
        if (!$dateNaissance) {
            return new JsonResponse(['error' => 'Format date de naissance invalide'], 400);
        }

        // Création du participant
        $participant = new Participant();
        $participant->setNom($nom);
        $participant->setPrenom($prenom);
        $participant->setDateNaissance($dateNaissance);
        $this->em->persist($participant);

        // Création de l’inscription (enrollment)
        $enrollment = new Enrollment();
        $enrollment->setUser($user);
        $enrollment->setParticipant($participant);
        $enrollment->setGroupe($atelier);
        $enrollment->setIsActive(true);
        $enrollment->setAnneeScolaire(date('Y'));
        $this->em->persist($enrollment);
        $this->em->flush();

        // Envoi de l’email de notification
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
