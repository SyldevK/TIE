<?php

namespace App\Controller;

use App\Repository\LogRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;


class LogController extends AbstractController
{
    #[Route('/api/logs', name: 'api_logs', methods: ['GET'])]
    #[IsGranted('ROLE_ADMIN')]
    public function index(LogRepository $logRepository): JsonResponse
    {
        $logs = $logRepository->findBy([], ['createdAt' => 'DESC'], 50);

        $data = array_map(fn($log) => [
            'id' => $log->getId(),
            'action' => $log->getAction(),
            'user' => $log->getUser()?->getEmail(),
            'createdAt' => $log->getCreatedAt()?->format('Y-m-d H:i'),
        ], $logs);

        return $this->json($data);
    }
}
