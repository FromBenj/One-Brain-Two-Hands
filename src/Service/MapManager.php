<?php

namespace App\Service;

use AllowDynamicProperties;
use App\Repository\AssociationRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

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
            $coordinatesData = $features[array_key_first($features)]['geometry']['coordinates'];

            return [
                'lat' => $coordinatesData[1],
                'long' => $coordinatesData[0],
            ];
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
                    $association->setCoordinates($coordinates);
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

    public function findByCircle(array $center, int $radiusMeters): array
    {

    }
}
