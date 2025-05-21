<?php

namespace App\EventSubscriber;

use App\Entity\Enrollment;
use Twig\Environment;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use ApiPlatform\Symfony\EventListener\EventPriorities;
use Symfony\Component\HttpKernel\Event\ViewEvent;
use Symfony\Component\HttpKernel\KernelEvents;

class EnrollmentEmailSubscriber implements EventSubscriberInterface
{
    private MailerInterface $mailer;
    private Environment $twig;

    public function __construct(MailerInterface $mailer, Environment $twig)
    {
        $this->mailer = $mailer;
        $this->twig = $twig;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::VIEW => ['onEnrollmentCreated', EventPriorities::POST_WRITE],
        ];
    }

    public function onEnrollmentCreated(ViewEvent $event): void
    {
        $enrollment = $event->getControllerResult();
        $method = $event->getRequest()->getMethod();

        if (!$enrollment instanceof Enrollment || $method !== 'POST') {
            return;
        }

        $html = $this->twig->render('emails/enrollment_notification.html.twig', [
            'participant' => $enrollment->getNomCompletParticipant(),
            'user' => $enrollment->getNomCompletUser(),
            'groupe' => $enrollment->getGroupe(),
            'annee' => $enrollment->getAnneeScolaire(),
        ]);

        $email = (new Email())
            ->from('noreply@tonsite.fr')
            ->to('latroupedesechappees@gmail.com')
            ->subject('Nouvelle inscription à un atelier')
            ->html($html);

        try {
            $this->mailer->send($email);
        } catch (\Throwable $e) {
            error_log('Erreur lors de l\'envoi de l\'email d\'inscription : ' . $e->getMessage());
        }
    }
}
