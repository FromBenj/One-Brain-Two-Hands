<?php

namespace App\Service;

use AllowDynamicProperties;
use App\Repository\AssociationRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use League\Geotools\Distance\Distance;
use League\Geotools\Coordinate\Coordinate;

#[AllowDynamicProperties]
class MapManager
{
    private const ENDPOINT = 'https://nominatim.openstreetmap.org/search';

    public function __construct(
        private readonly HttpClientInterface    $httpClient,
        private readonly EntityManagerInterface $entityManager,
        private readonly AssociationRepository  $associationRepository)
    {
    }

    public function getCoordFromAddress(string $address): ?array
    {
        if (trim($address) === '') {
            return null;
        }
        $response = $this->httpClient->request('GET', self::ENDPOINT, [
            'headers' => [
                'User-Agent' => 'OneBrainTwoHands/1.0 (https://github.com/FromBenj)',
            ],
            'query' => [
                'q' => $address,
                'format' => 'geojson',
                'limit' => 1
            ],
        ]);
        $data = json_decode($response->getContent(), true, 512, JSON_THROW_ON_ERROR);
        $features = $data['features'];
        if (!empty($features)) {

            return $features[array_key_first($features)]['geometry']['coordinates'];
        }

        return null;
    }

    public function migrateCoordToDatabase(int $limit): void
    {
        $associations = $this->associationRepository->findEmptyCoordinates($limit);
        if (!empty($associations)) {
            foreach ($associations as $association) {
                $address = $association->getAddress();
                $coordinates = $this->getCoordFromAddress($address);
                if ($coordinates) {
                    $association->setLatitude($coordinates[0]);
                    $association->setLongitude($coordinates[1]);
                    $this->entityManager->persist($association);
                } else {
                    continue;
                }
                sleep(1);
            }
            $this->entityManager->flush();
            $this->entityManager->clear();
        }
    }

    public function findByCircle(array $associations, array $center, int $radiusMeters): ?array
    {
        $filteredAsso = [];
        if (count($center) !== 2) {
            return null;
        }
        $centerPoint = new Coordinate($center);
        foreach ($associations as $association) {
          dd('yes');
            $point = new Coordinate($association->getCoordinates());
            $distance = (new Distance())->setFrom($centerPoint)->setTo($point);
            if ($distance->flat() < $radiusMeters) {
                $filteredAsso[] = $point;
            }
        }

        return $filteredAsso;
    }
}
