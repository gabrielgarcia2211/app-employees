<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Service\EmployeeService;

#[Route('/api', name: 'api_')]
class EmployeeController extends AbstractController
{
    private EmployeeService $employeeService;

    public function __construct(EmployeeService $employeeService)
    {
        $this->employeeService = $employeeService;
    }

    #[Route('/employees', name: 'get_employees', methods: ['GET'])]
    #[IsGranted('ROLE_ADMIN')]
    public function getEmployees(Request $request): JsonResponse
    {
        $name = $request->query->get('name');
        return $this->employeeService->getEmployees($name);
    }

    #[Route('/employe', name: 'get_employe', methods: ['GET'])]
    #[IsGranted('ROLE_USER')]
    public function getEmploye(): JsonResponse
    {
        return $this->employeeService->getEmploye($this->getUser());
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
        return $this->employeeService->editEmployeeName($id, $newName);
    }

    #[Route('/employees/{id}/position', name: 'edit_employee_position', methods: ['PUT'])]
    #[IsGranted('ROLE_USER')]
    public function editEmployeePosition(int $id, Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $newPosition = $data['position'] ?? null;
        return $this->employeeService->editEmployeePosition($id, $newPosition, $this->getUser());
    }

    #[Route('/employees/{id}', name: 'delete_employee', methods: ['DELETE'])]
    public function deleteEmployee(int $id): JsonResponse
    {
        return $this->employeeService->deleteEmployee($id, $this->getUser());
    }
}
