<?php

namespace App\Service;

use AllowDynamicProperties;
use App\Entity\Association;
use League\Csv\Reader;
use App\Repository\CategoryRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Serializer\SerializerInterface;

#[AllowDynamicProperties]
class AssociationManager
{
    public const FILE_START_NAME = "rna_waldec_20250901_dpt_";
    private array $departments = [];

    public function __construct(SerializerInterface $serializer, EntityManagerInterface $em,
                                CategoryRepository  $categoryRepository, MapManager $mapManager, string $projectDir)
    {
        $this->projectDir = $projectDir;
        $this->serializer = $serializer;
        $this->em = $em;
        $this->categoryRepository = $categoryRepository;
        $this->mapManager = $mapManager;
        $this->setDepartments();
    }

    public function cleanCSVValue(string $csvValue): string
    {
        $csvValue = trim($csvValue);
        $explodedValue = str_split($csvValue);
        if (isset($explodedValue[0]) && $explodedValue[0] === '"') {
            unset($explodedValue[0]);
        }
        $valueLength = strlen($csvValue);
        if (isset($explodedValue[$valueLength - 1]) && $explodedValue[$valueLength - 1] === '"') {
            unset($explodedValue[$valueLength - 1]);
        }

        return implode("", $explodedValue);
    }

    public function setDepartments(): array
    {
        $mainDepartments = range(1, 95);
        $specialDepartments = [98, 971, 972, 974, 976];

        $this->departments = array_merge($mainDepartments, $specialDepartments);

        return $this->departments;
    }

    public function getDataFromCSVPath(int $department): iterable
    {
        if (in_array($department, $this->departments, true)) {
            $filePath = $this->projectDir . '/src/Data/RNA/' . self::FILE_START_NAME . $department . '.csv';
            if (!file_exists($filePath)) {
                return;
            }
            $reader = Reader::from($filePath);
            $reader->setHeaderOffset(0);
            $reader->setDelimiter(';');
            $records = $reader->getRecords();
            foreach ($records as $association) {

                yield $association;
            }
        }
    }

    public function cleanAssociation(array $association): ?array
    {
        return array_map(fn($value) => $this->cleanCSVValue($value), $association);

    }

    public function getFinalSocialId(string $socialId, string $socialParentId): ?string
    {
        if (!empty($socialId)) {
            $category = $this->categoryRepository->findOneBy([
                'socialId' => $socialId,
            ]);

            return $category ? $socialId : null;
        }
        if (!empty($socialParentId)) {
            $category = $this->categoryRepository->findOneBy([
                'socialParentId' => $socialParentId,
            ]);

            return $category ? $socialParentId : null;
        }

        return null;
    }

    public function getCleanData(int $department): iterable
    {
        foreach ($this->getDataFromCSVPath($department) as $association) {
            $association = $this->cleanAssociation($association);

            if (empty($association['objet_social1']) && empty($association['objet_social2'])) {
                continue;
            }
            $finalSocialId = $this->getFinalSocialId($association['objet_social1'], $association['objet_social2']);
            if (!$finalSocialId) {
                continue;
            }
            $name = $association['titre_court'] ?? $association["titre"] ?? null;
            if (!$name) {
                continue;
            }
            $address = ($association['adrs_numvoie'] ?? '') . ' '
                . ($association['adrs_typevoie'] ?? '') . ' '
                . ($association['adrs_libvoie'] ?? '') . ' '
                . ($association['adrs_libcommune'] ?? '') . ' '
                . ($association['adrg_pays'] ?? '');
            $address = trim(str_replace('  ', ' ', $address));
            if ($address === '') {
                continue;
            }
            $coordinates = $this->mapManager->getCoordFromAddress($address);

            yield [
                'name' => $name,
                'activity' => $association['objet'] ?? '',
                'socialId' => $finalSocialId,
                'address' => $address,
                'website' => $association['siteweb'] ?? '',
                'latitude'=> $coordinates[0] ?? null,
                'longitude' => $coordinates[1] ?? null,
                'department' => $department,
            ];
        }
    }

    public function migrateToDatabase(int $department, ?int $limit): void
    {
        $generator = $this->getCleanData($department);
        $batchSize = 200;
        $i = 0;
        foreach ($generator as $assoData) {
            $association = new Association();
            $association->setName($assoData['name']);
            $association->setActivity($assoData['activity']);
            $association->setSocialId($assoData['socialId']);
            $association->setAddress($assoData['address']);
            $association->setWebsite($assoData['website']);
            $association->setLatitude($assoData['latitude']);
            $association->setLongitude($assoData['longitude']);
            $association->setDepartment($assoData['department']);
            $this->em->persist($association);
            // To check migration
            dump($association->getName());
            $i++;
            if (($i % $batchSize) === 0) {
                $this->em->flush();
                $this->em->clear();
                gc_collect_cycles();
            }
            if ($limit && $i === $limit) {
                break;
            }
        }
        $this->em->flush();
        $this->em->clear();
    }
}
