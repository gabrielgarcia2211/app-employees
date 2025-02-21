<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Service\RegistrationService;

#[Route('/api', name: 'api_')]
class AuthController extends AbstractController
{
    #[Route('/register', name: 'register', methods: ['POST'])]
    public function register(
        Request $request,
        RegistrationService $registrationService
    ): JsonResponse {
        $data = json_decode($request->getContent(), true);
        $result = $registrationService->register($data);

        return new JsonResponse(['message' => $result['message']], $result['status']);
    }
}
