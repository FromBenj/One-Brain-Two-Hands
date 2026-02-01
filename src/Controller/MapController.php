<?php

namespace App\Controller;

use AllowDynamicProperties;
use App\Repository\AssociationRepository;
use App\Service\AssociationManager;
use App\Service\MapManager;
use App\Service\NomenclatureManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\UX\Map\Circle;
use Symfony\UX\Map\Icon\Icon;
use Symfony\UX\Map\Map;
use Symfony\UX\Map\Marker;
use Symfony\UX\Map\Point;
use Symfony\UX\Map\InfoWindow;


#[AllowDynamicProperties]
#[Route('/map', name: 'map_')]
final class MapController extends AbstractController
{
    public function __construct(NomenclatureManager   $nomenclatureManager,
                                AssociationManager    $associationManager,
                                MapManager            $mapManager,
                                AssociationRepository $associationRepository
    )
    {
        $this->nomenclatureManager = $nomenclatureManager;
        $this->associationsManager = $associationManager;
        $this->mapManager = $mapManager;
        $this->associationRepository = $associationRepository;
    }

    #[Route('/you', name: 'user_position', methods: ['GET', 'POST'])]
    public function fromCurrentUser(Request $request): Response
    {
        $session = $request->getSession();
        if ($request->getMethod() === 'POST') {
            $lat = $request->request->get('lat');
            $lon = $request->request->get('lon');
            $accuracy = $request->request->get('accuracy');
            $userPosition = [
                'lat' => $lat,
                'lon' => $lon,
                'accuracy' => $accuracy,
            ];
            $session->set('user_position', $userPosition);

            return $this->json(['success' => true, 'lat' => $lat, 'lon' => $lon, 'accuracy' => $accuracy]);
        }
        $userMap = null;
        if ($session->get('user_position') !== null) {
            $userPosition = $session->get('position');
            $userLat = $userPosition['lat'];
            $userLon = $userPosition['lon'];
            $icon = Icon::ux('streamline-flex:pin-1-solid');
            $userMap = (new Map())
                ->center(new Point($userLat, $userLon))
                ->zoom(12)
                ->addMarker(
                    new Marker(
                        position: new Point($userLat, $userLon),
                        icon: $icon
                    )
                );

            $userMap->addCircle(new Circle(
                center: new Point($userLat, $userLon),
                radius: 2_000,
                infoWindow: new InfoWindow(
                    content: 'All associations within 2 km of you',
                ),
            ));
        }
        return $this->render('map/user.html.twig',
            ['user_map' => $userMap]
        );
    }

//    #[Route('/department/{}', name: 'department')]


    #[Route('/city/{city}', name: 'city')]
    public function city(string $city): Response
    {
        if ($city === 'bordeaux') {
            $bordeauxRadius = 8000;
            $associations = $this->associationRepository->findAll();
            $points = $this->mapManager->findByCircle($associations, [44.841225, -0.5800364], $bordeauxRadius);
            dd($points);
        }
        dd();
    }

    #[Route('/test', name: 'test')]
    public function test(): Response
    {
        $associations = $this->associationRepository->findAll();
        dd($this->associationRepository->findEmptyCoordinates(50));
    }

}
