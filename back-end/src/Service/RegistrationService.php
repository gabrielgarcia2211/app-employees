<?php

namespace App\Service;

use App\Entity\User;
use App\Entity\Employee;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use App\Service\PositionService;
use Symfony\Component\HttpFoundation\JsonResponse;

class RegistrationService
{
    private $em;
    private $passwordHasher;
    private $positionService;
    private $emailService;

    public function __construct(
        EntityManagerInterface $em,
        UserPasswordHasherInterface $passwordHasher,
        PositionService $positionService,
        EmailService $emailService
    ) {
        $this->em = $em;
        $this->passwordHasher = $passwordHasher;
        $this->positionService = $positionService;
        $this->emailService = $emailService;
    }

    public function register(array $data): JsonResponse
    {
        if (!$this->isValidRegistrationData($data)) {
            return new JsonResponse(['error' => 'Datos incompletos'], JsonResponse::HTTP_BAD_REQUEST);
        }

        if ($this->userExists($data['email'])) {
            return new JsonResponse(['error' => 'El usuario ya existe'], JsonResponse::HTTP_BAD_REQUEST);
        }

        if (!$this->positionService->isValidPosition($data['position'])) {
            return new JsonResponse(['error' => 'Posición no válida'], JsonResponse::HTTP_BAD_REQUEST);
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

            // Enviar correo de bienvenida
            $this->emailService->sendWelcomeEmail($user->getEmail(), $employee->getName());

            return new JsonResponse(['message' => 'Usuario y empleado registrados con éxito'], JsonResponse::HTTP_CREATED);
        } catch (\Exception $e) {
            $this->em->rollback();
            return new JsonResponse(['message' => 'Error al registrar usuario y empleado'], JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    private function isValidRegistrationData(?array $data): bool
    {
        return isset($data['email'], $data['password'], $data['name'], $data['lastname'], $data['position']) &&
               !empty($data['email']) && !empty($data['password']) && !empty($data['name']) && !empty($data['lastname']) && !empty($data['position']);
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
        if(isset($data['birthdate'])){
            $employee->setBirthdate(new \DateTime($data['birthdate']));
        }
        return $employee;
    }
}
