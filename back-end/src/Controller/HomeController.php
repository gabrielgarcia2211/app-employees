<?php

namespace App\Controller;

use App\Entity\User;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

final class HomeController extends AbstractController
{
    #[Route('/api/home', name: 'app_home')]
    public function getUsers(ManagerRegistry $doctrine): JsonResponse
    {
        $entityManager = $doctrine->getRepository(User::class);
        $users = $entityManager->findAll();

        $userArray = [];
        foreach ($users as $user) {
            $userArray[] = [
                'id' => $user->getId(),
                'email' => $user->getEmail(),
            ];
        }

        return $this->json($userArray);
    }
}
