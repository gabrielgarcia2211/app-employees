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

    public function getEmployees(?string $name): JsonResponse
    {
        $employees = $name ? $this->employeeRepository->searchByName($name) : $this->employeeRepository->findAll();

        if (empty($employees)) {
            return new JsonResponse(['message' => 'No se encontraron empleados'], JsonResponse::HTTP_NOT_FOUND);
        }

        $data = array_map(fn($employee) => [
            'id' => $employee->getId(),
            'name' => $employee->getName(),
            'lastname' => $employee->getLastname(),
            'position' => $employee->getPosition(),
            'birthdate' => $employee->getBirthdate() ? $employee->getBirthdate()->format('Y-m-d') : null,
            'email' => $employee->getUser() ? $employee->getUser()->getEmail() : null
        ], $employees);

        return new JsonResponse($data);
    }

    public function getPositions(PositionService $positionService): JsonResponse
    {
        try {
            $positions = $positionService->getPositions();
            return new JsonResponse($positions);
        } catch (\Exception $e) {
            return new JsonResponse(['error' => $e->getMessage()], JsonResponse::HTTP_BAD_REQUEST);
        }
    }

    public function editEmployeeName(int $id, string $newName): JsonResponse
    {
        if (!$newName) {
            return new JsonResponse(['error' => 'El nombre es requerido'], JsonResponse::HTTP_BAD_REQUEST);
        }
        try {
            $employee = $this->employeeRepository->find($id);
            if (!$employee) {
                return new JsonResponse(['error' => 'Empleado no encontrado'], JsonResponse::HTTP_NOT_FOUND);
            }
            $employee->setName($newName);
            $this->entityManager->flush();
            return new JsonResponse(['message' => 'Nombre del empleado actualizado correctamente']);
        } catch (\Exception $e) {
            return new JsonResponse(['error' => $e->getMessage()], JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function editEmployeePosition(int $id, string $newPosition, $currentUser): JsonResponse
    {
        if (!$newPosition) {
            return new JsonResponse(['error' => 'El puesto de trabajo es requerido'], JsonResponse::HTTP_BAD_REQUEST);
        }

        try {
            $employee = $this->employeeRepository->find($id);
            if (!$employee) {
                return new JsonResponse(['error' => 'Empleado no encontrado'], JsonResponse::HTTP_NOT_FOUND);
            }
            if ($employee->getUser() === null || $employee->getUser()->getId() !== $currentUser->getId()) {
                return new JsonResponse(['error' => 'No tienes permiso para modificar este empleado'], JsonResponse::HTTP_FORBIDDEN);
            }
            if (!$this->positionService->isValidPosition($newPosition)) {
                throw new \InvalidArgumentException('Puesto de trabajo no válido');
            }
            $employee->setPosition($newPosition);
            $this->entityManager->flush();
            return new JsonResponse(['message' => 'Puesto de trabajo del empleado actualizado correctamente']);
        } catch (\Exception $e) {
            return new JsonResponse(['error' => $e->getMessage()], JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function deleteEmployee(int $id, $currentUser): JsonResponse
    {
        try {
            $employee = $this->employeeRepository->find($id);
            if (!$employee) {
                return new JsonResponse(['error' => 'Empleado no encontrado'], JsonResponse::HTTP_NOT_FOUND);
            }
            if (!$currentUser->isGranted('ROLE_ADMIN') && $employee->getUser()->getId() !== $currentUser->getId()) {
                return new JsonResponse(['error' => 'No tienes permiso para eliminar este empleado'], JsonResponse::HTTP_FORBIDDEN);
            }
            $this->entityManager->remove($employee);
            $this->entityManager->flush();
            return new JsonResponse(['message' => 'Empleado eliminado correctamente']);
        } catch (\Exception $e) {
            return new JsonResponse(['error' => $e->getMessage()], JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
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
