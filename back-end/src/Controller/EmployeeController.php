<?php

namespace App\Controller;

use App\Entity\Employee;
use App\Service\PositionService;
use App\Repository\EmployeeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Repository\UserRepository;


#[Route('/api', name: 'api_')]
class EmployeeController extends AbstractController
{
    #[Route('/employees', name: 'get_employees', methods: ['GET'])]
    #[IsGranted('ROLE_ADMIN')]
    public function getEmployees(Request $request, EmployeeRepository $employeeRepository): JsonResponse
    {
        $name = $request->query->get('name');

        if ($name) {
            $employees = $employeeRepository->searchByName($name);
        } else {
            $employees = $employeeRepository->findAll();
        }

        if (empty($employees)) {
            return $this->json(['message' => 'No se encontraron empleados'], JsonResponse::HTTP_NOT_FOUND);
        }

        $data = [];

        foreach ($employees as $employee) {
            $data[] = [
                'id' => $employee->getId(),
                'name' => $employee->getName(),
                'lastname' => $employee->getLastname(),
                'position' => $employee->getPosition(),
                'email' => $employee->getUser()->getEmail()
            ];
        }

        return $this->json($data);
    }

    #[Route('/positions', name: 'get_positions', methods: ['GET'])]
    #[IsGranted('ROLE_ADMIN')]
    public function getPositions(PositionService $positionService): JsonResponse
    {
        try {
            $positions = $positionService->getPositions();
            return $this->json($positions);
        } catch (\Exception $e) {
            return $this->json(['error' => $e->getMessage()], JsonResponse::HTTP_BAD_REQUEST);
        }
    }

    #[Route('/employees', name: 'register_employee', methods: ['POST'])]
    #[IsGranted('ROLE_ADMIN')]
    public function registerEmployee(
        Request $request,
        PositionService $positionService,
        EntityManagerInterface $entityManager,
        UserRepository $userRepository,
        EmployeeRepository $employeeRepository
    ): JsonResponse {
        $data = json_decode($request->getContent(), true);

        if (!isset($data['name'], $data['lastname'], $data['position'], $data['birthdate'])) {
            return $this->json(['error' => 'Datos incompletos'], JsonResponse::HTTP_BAD_REQUEST);
        }

        if (!$positionService->isValidPosition($data['position'])) {
            return $this->json(['error' => 'Puesto de trabajo no válido'], JsonResponse::HTTP_BAD_REQUEST);
        }

        if (isset($data['user_id'])) {
            $user = $userRepository->find($data['user_id']);
            if (!$user || !$user->hasRole('ROLE_USER')) {
                return $this->json(['error' => 'Usuario no válido o no tiene el rol adecuado'], JsonResponse::HTTP_BAD_REQUEST);
            }

            $existingEmployee = $employeeRepository->findOneBy(['user' => $user]);
            if ($existingEmployee) {
                return $this->json(['error' => 'El usuario ya está asignado a otro empleado'], JsonResponse::HTTP_BAD_REQUEST);
            }
        }

        $employee = new Employee();
        $employee->setName($data['name']);
        $employee->setLastname($data['lastname']);
        $employee->setPosition($data['position']);
        $employee->setBirthdate(new \DateTime($data['birthdate']));

        if (isset($user)) {
            $employee->setUser($user);
        }

        $entityManager->persist($employee);
        $entityManager->flush();

        return $this->json(['message' => 'Empleado registrado con éxito'], JsonResponse::HTTP_CREATED);
    }
}
