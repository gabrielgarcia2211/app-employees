<?php

namespace App\Controller;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Service\UserService;

#[Route('/api', name: 'api_')]
class UserController extends AbstractController
{
    private UserService $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    #[Route('/users', name: 'get_users', methods: ['GET'])]
    #[IsGranted('ROLE_ADMIN')]
    public function getUsers(): JsonResponse
    {
        return $this->userService->getUsers();
    }
}
