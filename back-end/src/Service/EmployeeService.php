<?php

namespace App\Service;

use App\Entity\Employee;
use App\Repository\UserRepository;
use App\Repository\EmployeeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;

class EmployeeService
{
    private $positionService;
    private $entityManager;
    private $userRepository;
    private $employeeRepository;

    public function __construct(
        PositionService $positionService,
        EntityManagerInterface $entityManager,
        UserRepository $userRepository,
        EmployeeRepository $employeeRepository
    ) {
        $this->positionService = $positionService;
        $this->entityManager = $entityManager;
        $this->userRepository = $userRepository;
        $this->employeeRepository = $employeeRepository;
    }

    public function registerEmployee(array $data, $currentUser): JsonResponse
    {
        if (!$this->isValidEmployeeData($data)) {
            return new JsonResponse(['error' => 'Datos incompletos o puesto de trabajo no válido'], JsonResponse::HTTP_BAD_REQUEST);
        }

        $user = $this->getUserForEmployee($data, $currentUser);
        if ($user instanceof JsonResponse) {
            return $user;
        }

        $employee = $this->createEmployee($data, $user);
        $this->entityManager->persist($employee);
        $this->entityManager->flush();

        return new JsonResponse(['message' => 'Empleado registrado con éxito'], JsonResponse::HTTP_CREATED);
    }

    public function updateEmployeeName(Employee $employee, string $newName): void
    {
        $employee->setName($newName);
        $this->entityManager->flush();
    }

    public function updateEmployeePosition(Employee $employee, string $newPosition): void
    {
        if (!$this->positionService->isValidPosition($newPosition)) {
            throw new \InvalidArgumentException('Puesto de trabajo no válido');
        }

        $employee->setPosition($newPosition);
        $this->entityManager->flush();
    }

    private function isValidEmployeeData(array $data): bool
    {
        return isset($data['name'], $data['lastname'], $data['position'], $data['birthdate']) &&
               $this->positionService->isValidPosition($data['position']);
    }

    private function getUserForEmployee(array $data, $currentUser)
    {
        if ($currentUser->isGranted('ROLE_ADMIN') && !isset($data['user_id'])) {
            return null;
        }

        if (!isset($data['user_id'])) {
            return new JsonResponse(['error' => 'user_id es requerido para roles no administradores'], JsonResponse::HTTP_BAD_REQUEST);
        }

        if (!$currentUser->isGranted('ROLE_ADMIN') && $data['user_id'] !== $currentUser->getId()) {
            return new JsonResponse(['error' => 'user_id debe ser igual al ID del usuario autenticado'], JsonResponse::HTTP_BAD_REQUEST);
        }

        $user = $this->userRepository->find($data['user_id']);
        if (!$user || !$user->hasRole('ROLE_USER')) {
            return new JsonResponse(['error' => 'Usuario no válido o no tiene el rol adecuado'], JsonResponse::HTTP_BAD_REQUEST);
        }

        $existingEmployee = $this->employeeRepository->findOneBy(['user' => $user]);
        if ($existingEmployee) {
            return new JsonResponse(['error' => 'El usuario ya está asignado a otro empleado'], JsonResponse::HTTP_BAD_REQUEST);
        }

        return $user;
    }

    private function createEmployee(array $data, $user): Employee
    {
        $employee = new Employee();
        $employee->setName($data['name']);
        $employee->setLastname($data['lastname']);
        $employee->setPosition($data['position']);
        $employee->setBirthdate(new \DateTime($data['birthdate']));

        if ($user) {
            $employee->setUser($user);
        }

        return $employee;
    }
}
