<?php

namespace App\Controller;

use App\Service\PositionService;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;

class PositionController extends AbstractController
{
    private $positionService;

    public function __construct(PositionService $positionService)
    {
        $this->positionService = $positionService;
    }

    #[Route('/positions', name: 'get_positions', methods: ['GET'])]
    public function getPositions(): Response
    {
        $positions = $this->positionService->getPositions();
        return new Response(
            json_encode($positions),
            Response::HTTP_OK,
            ['content-type' => 'application/json']
        );
    }
}
