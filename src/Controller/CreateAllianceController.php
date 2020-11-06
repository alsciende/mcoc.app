<?php

namespace App\Controller;

use App\Form\CreateAllianceType;
use App\Form\Model\CreateAlliance;
use App\Service\AllianceService;
use App\Service\SessionService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CreateAllianceController extends AbstractController
{
    /**
     * @Route("/create/alliance", name="create_alliance")
     */
    public function index(
        Request $request,
        AllianceService $service,
        SessionService $sessionService
    ): Response {
        $player = $sessionService->getActivePlayer();
        $data = new CreateAlliance();

        $form = $this->createForm(CreateAllianceType::class, $data);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var CreateAlliance $data */
            $data = $form->getData();
            $service->createAlliance($player, $data->tag, $data->name);

            return $this->redirectToRoute('home');
        }

        return $this->render('create_alliance/index.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
