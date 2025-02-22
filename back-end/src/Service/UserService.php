<?php

namespace App\Service;

use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\JsonResponse;

class UserService
{
    private $userRepository;

    public function __construct(
        UserRepository $userRepository
    ) {
        $this->userRepository = $userRepository;
    }

    public function getUsers(): JsonResponse
    {
        $employees = $this->userRepository->findAll();
        if (empty($employees)) {
            return new JsonResponse(['message' => 'No se encontraron empleados'], JsonResponse::HTTP_NOT_FOUND);
        }
        $data = array_filter($employees, fn($employee) => in_array('ROLE_USER', $employee->getRoles()));
        $data = array_map(fn($employee) => [
            'id' => $employee->getId(),
            'email' => $employee->getUserIdentifier()
        ], $data);
        $data = array_values($data);
        return new JsonResponse($data);
    }
}
