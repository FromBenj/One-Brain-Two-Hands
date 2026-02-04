<?php

namespace App\Service;

use AllowDynamicProperties;
use App\Entity\Category;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Serializer\SerializerInterface;

#[AllowDynamicProperties]
class NomenclatureManager
{
    public const  FILE_NAME = "custom-nomenclature-waldec-0126.csv";
    public function __construct(SerializerInterface $serializer, EntityManagerInterface $em, string $projectDir)
    {
        $this->serializer = $serializer;
        $this->em = $em;
        $this->projectDir = $projectDir;
    }

    public function getDataFromCSVPath(): array
    {
        $filePath = $this->projectDir . '/src/Data/waldec/' . self::FILE_NAME;
        $csvData = file_get_contents($filePath);

        return $this->serializer->decode($csvData, 'csv');
    }

    public function getCleanData(array $initialData): ?array
    {
        $cleanData = [];
        foreach ($initialData as $initialDatum) {
            $datum = [
                'socialParentId' => $initialDatum['objet_social_parent_id'],
                'socialId' => $initialDatum['objet_social_id'],
                'name' => $initialDatum['objet_social_libelle'],
            ];
            $cleanData[] = $datum;
        }

        return $cleanData;
    }

    public function createEntities(array $data): array
    {
        $entities = [];
        foreach ($data as $row) {
            $category = new Category();
            $category
                ->setSocialParentId((int)$row['socialParentId'])
                ->setSocialId((int)$row['socialId'])
                ->setName($row['name']);
        $entities[]= $category;
        }

        return $entities;
    }

    public function migrateToDatabase(): void
    {
        $data = $this->getDataFromCSVPath();
        $cleanData = $this->getCleanData($data);
        $categories = $this->createEntities($cleanData);
        foreach($categories as $category) {
            $this->em->persist($category);
        }

        $this->em->flush();
    }
}
