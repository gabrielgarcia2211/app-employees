<?php

use App\Entity\User;
use App\Service\EmployeeService;
use App\Service\PositionService;
use App\Repository\UserRepository;
use App\Controller\EmployeeController;
use App\Repository\EmployeeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class EmployeeControllerTest extends KernelTestCase
{
    private EmployeeController $controller;
    private EmployeeService $employeeService;
    private EmployeeRepository $employeeRepository;
    private EntityManagerInterface $entityManager;

    protected function setUp(): void
    {
        self::bootKernel();
        $container = static::getContainer();

        $this->entityManager = $container->get(EntityManagerInterface::class);
        $this->employeeRepository = $container->get(EmployeeRepository::class);
        $positionService = $container->get(PositionService::class);
        $userRepository = $container->get(UserRepository::class);

        $this->employeeService = new EmployeeService(
            $positionService,
            $this->entityManager,
            $userRepository,
            $this->employeeRepository
        );

        $this->controller = new EmployeeController($this->employeeService);
    }

    public function testGetEmployeesWithData()
    {
        $request = new Request(query: ['name' => 'John']);
        $response = $this->controller->getEmployees($request);

        $this->assertInstanceOf(JsonResponse::class, $response);
        $data = json_decode($response->getContent(), true);
        $this->assertIsArray($data);
        $this->assertNotEmpty($data);
    }
}
