<?php

namespace App\Controller;

use App\Entity\Champion;
use App\Entity\Roster;
use App\Form\Model\PlayerChampion;
use App\Form\Model\PlayerChampionCollection;
use App\Form\PlayerChampionCollectionType;
use App\Service\PlayerService;
use App\Service\SessionService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class EditRosterController extends AbstractController
{
    /**
     * @Route("/edit/roster/{tier<\d+>}", name="edit_roster")
     */
    public function index(
        int $tier,
        Request $request,
        SessionService $sessionService,
        PlayerService $playerService
    ) {
        $player = $sessionService->getActivePlayer();

        $collection = new PlayerChampionCollection();

        $list = $playerService->listChampionsAndRosters($player, $tier);

        foreach ($list as $id => $row) {
            /** @var Champion $champion */
            $champion = $row['champion'];
            /** @var Roster|null $roster */
            $roster = $row['roster'];

            // Form Model data object
            $playerChampion = new PlayerChampion(
                $player->getId(),
                $champion->getId(),
                $champion->getCharacter()->getName(),
                $champion->getCharacter()->getType()
            );

            if ($roster instanceof Roster) {
                $playerChampion->setChecked(true);
                $playerChampion->setRank($roster->getRank());
                $playerChampion->setSignature($roster->getSignature());
            }

            $collection->addChampion($playerChampion);
        }

        $form = $this->createForm(PlayerChampionCollectionType::class, $collection);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var PlayerChampionCollection $collection */
            $collection = $form->getData();

            foreach ($collection->getChampions() as $playerChampion) {
                // data from persistence
                $row = $list[$playerChampion->getChampionId()];
                $champion = $row['champion'];
                $roster = $row['roster'];

                // see if we got a Roster for that champion earlier
                if ($roster instanceof Roster) {
                    if ($playerChampion->isChecked() === true) {
                        $playerService->updateChampion($roster, $playerChampion->getRank(), $playerChampion->getSignature());
                    } else {
                        $playerService->removeChampion($roster);
                    }
                } else {
                    if ($playerChampion->isChecked() === true) {
                        $playerService->addChampion($player, $champion, $playerChampion->getRank(), $playerChampion->getSignature());
                    }
                }
            }
        }

        return $this->render('edit_roster/index.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
