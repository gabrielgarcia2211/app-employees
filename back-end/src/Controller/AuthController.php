<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Employee;
use App\Repository\UserRepository;
use App\Repository\EmployeeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use App\Service\PositionService;

#[Route('/api', name: 'api_')]
class AuthController extends AbstractController
{
    #[Route('/register', name: 'register', methods: ['POST'])]
    public function register(
        Request $request,
        EntityManagerInterface $em,
        UserPasswordHasherInterface $passwordHasher,
        PositionService $positionService
    ): JsonResponse {
        $data = json_decode($request->getContent(), true);

        if (!$this->isValidRegistrationData($data)) {
            return new JsonResponse(['message' => 'Datos incompletos'], 400);
        }

        if ($this->userExists($em, $data['email'])) {
            return new JsonResponse(['message' => 'El usuario ya existe'], 400);
        }

        if (!$positionService->isValidPosition($data['position'])) {
            return new JsonResponse(['message' => 'Posición no válida'], 400);
        }

        $em->beginTransaction();
        try {
            $user = $this->createUser($data['email'], $data['password'], $passwordHasher);
            $em->persist($user);
            $em->flush();

            $employee = $this->createEmployee($user, $data);
            $em->persist($employee);
            $em->flush();

            $em->commit();
            return new JsonResponse(['message' => 'Usuario y empleado registrados con éxito'], 201);
        } catch (\Exception $e) {
            $em->rollback();
            return new JsonResponse(['message' => 'Error al registrar usuario y empleado'], 500);
        }
    }

    private function isValidRegistrationData(?array $data): bool
    {
        return isset($data['email'], $data['password'], $data['name'], $data['lastname'], $data['position']);
    }

    private function userExists(EntityManagerInterface $em, string $email): bool
    {
        return (bool) $em->getRepository(User::class)->findOneBy(['email' => $email]);
    }

    private function createUser(string $email, string $password, UserPasswordHasherInterface $passwordHasher): User
    {
        $user = new User();
        $user->setEmail($email);
        $hashedPassword = $passwordHasher->hashPassword($user, $password);
        $user->setPassword($hashedPassword);
        $user->setRoles(['ROLE_USER']);
        return $user;
    }

    private function createEmployee(User $user, array $data): Employee
    {
        $employee = new Employee();
        $employee->setUser($user);
        $employee->setName($data['name']);
        $employee->setLastname($data['lastname']);
        $employee->setPosition($data['position']);
        $employee->setBirthdate(new \DateTime($data['birthdate']));
        return $employee;
    }
}
