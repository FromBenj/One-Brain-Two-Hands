<?php

namespace App\Controller;

use AllowDynamicProperties;
use App\Service\AssociationManager;
use App\Service\MapManager;
use App\Service\NomenclatureManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[AllowDynamicProperties]
#[Route('/map', name: 'map_')]
final class MapController extends AbstractController
{
    public function __construct(NomenclatureManager $nomenclatureManager,
                                AssociationManager  $associationManager,
                                MapManager          $mapManager)
    {
        $this->nomenclatureManager = $nomenclatureManager;
        $this->associationsManager = $associationManager;
        $this->mapManager = $mapManager;
    }

    #[Route('/', name: 'user', methods: ['GET', 'POST'])]
    public function fromCurrentUser(Request $request): Response
    {
//        dd($this->mapManager->getCoordFromAddress('Bordeaux'));
//        $data = [];
//        if ($request->isMethod('POST')) {
//            try {
//                $data = json_decode($request->getContent(), true, 512, JSON_THROW_ON_ERROR);
//            dump($data);
//            } catch (\JsonException $error) {
//                return $this->json(['error' => 'Invalid JSON'], 400);
//            }
//            if (!isset($data['lat'], $data['lon'])) {
//                return $this->json(['error' => 'Invalid payload'],400);
//            }
//        }
//        dd($data);

        return $this->render('data/index.html.twig', [
//            'data' => $data,
        ]);
    }

    #[Route('/department/{}', name: 'department')]
    public function fromDepartment(): Response
    {
        $this->associationsManager->migrateToDatabase(33);
        dd();
    }
}
