<?php

namespace App\Service;

use App\Entity\User;
use App\Entity\Employee;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use App\Service\PositionService;

class RegistrationService
{
    private $em;
    private $passwordHasher;
    private $positionService;

    public function __construct(
        EntityManagerInterface $em,
        UserPasswordHasherInterface $passwordHasher,
        PositionService $positionService
    ) {
        $this->em = $em;
        $this->passwordHasher = $passwordHasher;
        $this->positionService = $positionService;
    }

    public function register(array $data): array
    {
        if (!$this->isValidRegistrationData($data)) {
            return ['status' => 400, 'message' => 'Datos incompletos'];
        }

        if ($this->userExists($data['email'])) {
            return ['status' => 400, 'message' => 'El usuario ya existe'];
        }

        if (!$this->positionService->isValidPosition($data['position'])) {
            return ['status' => 400, 'message' => 'Posición no válida'];
        }

        $this->em->beginTransaction();
        try {
            $user = $this->createUser($data['email'], $data['password']);
            $this->em->persist($user);
            $this->em->flush();

            $employee = $this->createEmployee($user, $data);
            $this->em->persist($employee);
            $this->em->flush();

            $this->em->commit();
            return ['status' => 201, 'message' => 'Usuario y empleado registrados con éxito'];
        } catch (\Exception $e) {
            $this->em->rollback();
            return ['status' => 500, 'message' => 'Error al registrar usuario y empleado'];
        }
    }

    private function isValidRegistrationData(?array $data): bool
    {
        return isset($data['email'], $data['password'], $data['name'], $data['lastname'], $data['position']);
    }

    private function userExists(string $email): bool
    {
        return (bool) $this->em->getRepository(User::class)->findOneBy(['email' => $email]);
    }

    private function createUser(string $email, string $password): User
    {
        $user = new User();
        $user->setEmail($email);
        $hashedPassword = $this->passwordHasher->hashPassword($user, $password);
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
