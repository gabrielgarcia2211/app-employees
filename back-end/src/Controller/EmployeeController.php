<?php

namespace App\Controller;

use App\Service\PositionService;
use App\Repository\EmployeeRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Service\EmployeeService;

#[Route('/api', name: 'api_')]
class EmployeeController extends AbstractController
{
    private EmployeeRepository $employeeRepository;
    private EmployeeService $employeeService;

    public function __construct(EmployeeRepository $employeeRepository, EmployeeService $employeeService)
    {
        $this->employeeRepository = $employeeRepository;
        $this->employeeService = $employeeService;
    }

    #[Route('/employees', name: 'get_employees', methods: ['GET'])]
    #[IsGranted('ROLE_ADMIN')]
    public function getEmployees(Request $request): JsonResponse
    {
        $name = $request->query->get('name');
        $employees = $name ? $this->employeeRepository->searchByName($name) : $this->employeeRepository->findAll();

        if (empty($employees)) {
            return $this->json(['message' => 'No se encontraron empleados'], JsonResponse::HTTP_NOT_FOUND);
        }

        $data = array_map(fn($employee) => [
            'id' => $employee->getId(),
            'name' => $employee->getName(),
            'lastname' => $employee->getLastname(),
            'position' => $employee->getPosition(),
            'email' => $employee->getUser()->getEmail()
        ], $employees);

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
    public function registerEmployee(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        return $this->employeeService->registerEmployee($data, $this->getUser());
    }

    #[Route('/employees/{id}/name', name: 'edit_employee_name', methods: ['PUT'])]
    #[IsGranted('ROLE_ADMIN')]
    public function editEmployeeName(int $id, Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $newName = $data['name'] ?? null;
        if (!$newName) {
            return $this->json(['error' => 'El nombre es requerido'], JsonResponse::HTTP_BAD_REQUEST);
        }
        try {
            $employee = $this->employeeRepository->find($id);
            if (!$employee) {
                return $this->json(['error' => 'Empleado no encontrado'], JsonResponse::HTTP_NOT_FOUND);
            }

            $this->employeeService->updateEmployeeName($employee, $newName);
            return $this->json(['message' => 'Nombre del empleado actualizado correctamente']);
        } catch (\Exception $e) {
            return $this->json(['error' => $e->getMessage()], JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    #[Route('/employees/{id}/position', name: 'edit_employee_position', methods: ['PUT'])]
    #[IsGranted('ROLE_USER')]
    public function editEmployeePosition(int $id, Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $newPosition = $data['position'] ?? null;

        if (!$newPosition) {
            return $this->json(['error' => 'El puesto de trabajo es requerido'], JsonResponse::HTTP_BAD_REQUEST);
        }

        try {
            $employee = $this->employeeRepository->find($id);
            if (!$employee) {
                return $this->json(['error' => 'Empleado no encontrado'], JsonResponse::HTTP_NOT_FOUND);
            }
            if ($employee->getUser()->getId() !== $this->getUser()->getId()) {
                return $this->json(['error' => 'No tienes permiso para modificar este empleado'], JsonResponse::HTTP_FORBIDDEN);
            }
            $this->employeeService->updateEmployeePosition($employee, $newPosition);
            return $this->json(['message' => 'Puesto de trabajo del empleado actualizado correctamente']);
        } catch (\Exception $e) {
            return $this->json(['error' => $e->getMessage()], JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    #[Route('/employees/{id}', name: 'delete_employee', methods: ['DELETE'])]
    public function deleteEmployee(int $id): JsonResponse
    {
        try {
            $employee = $this->employeeRepository->find($id);
            if (!$employee) {
                return $this->json(['error' => 'Empleado no encontrado'], JsonResponse::HTTP_NOT_FOUND);
            }
            if (!$this->isGranted('ROLE_ADMIN') && $employee->getUser()->getId() !== $this->getUser()->getId()) {
                return $this->json(['error' => 'No tienes permiso para eliminar este empleado'], JsonResponse::HTTP_FORBIDDEN);
            }
            $this->employeeService->deleteEmployee($employee);
            return $this->json(['message' => 'Empleado eliminado correctamente']);
        } catch (\Exception $e) {
            return $this->json(['error' => $e->getMessage()], JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
