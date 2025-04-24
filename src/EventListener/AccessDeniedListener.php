<?php

namespace App\EventListener;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Lexik\Bundle\JWTAuthenticationBundle\Exception\JWTDecodeFailureException;
use Lexik\Bundle\JWTAuthenticationBundle\Exception\MissingTokenException;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;

#[AsEventListener(event: 'kernel.exception')]
class AccessDeniedListener
{
    public function __construct(private LoggerInterface $logger) {}

    public function onKernelException(ExceptionEvent $event): void
    {
        $exception = $event->getThrowable();

        // Pour journaliser l'erreur avec plus de contexte
        $this->logger->warning('Exception capturée dans AccessDeniedListener', [
            'exception' => $exception::class,
            'message' => $exception->getMessage(),
            'path' => $event->getRequest()->getPathInfo(),
            'method' => $event->getRequest()->getMethod(),
        ]);

        if (
            $exception instanceof AccessDeniedException ||
            $exception instanceof AccessDeniedHttpException
        ) {
            $event->setResponse(new JsonResponse([
                'code' => 403,
                'message' => 'Accès refusé : vous n\'avez pas la permission pour cette ressource.'
            ], 403));
        }

        if (
            $exception instanceof MissingTokenException ||
            $exception instanceof JWTDecodeFailureException
        ) {
            $event->setResponse(new JsonResponse([
                'code' => 401,
                'message' => 'Jeton d\'authentification manquant ou invalide.'
            ], 401));
        }
    }
}
