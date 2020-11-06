<?php

namespace App\Controller;

use App\Service\AllianceService;
use App\Service\SessionService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class LeaveAllianceController extends AbstractController
{
    /**
     * @Route("/leave/alliance", name="leave_alliance")
     */
    public function index(
        SessionService $sessionService,
        AllianceService $allianceService
    ): Response {
        $player = $sessionService->getActivePlayer();
        $member = $player->getMember();

        if ($member === null) {
            throw $this->createNotFoundException("No alliance");
        }

        $allianceService->removeMember($member);

        return $this->render('leave_alliance/index.html.twig', [
            'controller_name' => 'LeaveAllianceController',
        ]);
    }
}
