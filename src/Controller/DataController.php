<?php

namespace App\Controller;

use AllowDynamicProperties;
use App\Service\NomenclatureManager;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[AllowDynamicProperties]
#[Route('/data', name: 'data_')]
final class DataController
{
    public const string VERSION = '/rna_import_20250901_dpt_';

    public function __construct(NomenclatureManager $nomenclatureManager) {
        $this->nomenclatureManager = $nomenclatureManager;
    }

    #[Route('/nomenclature', name: 'nomenclature')]
    public function getNomenclature(): Response
    {
$data = $this->nomenclatureManager->getDataFromCSVPath();
$cleanData = $this->nomenclatureManager->getCleanData($data);
//
//        $nomenclature = $this->nomenclatureManager->importDataFromCSVPath($fileName);

        dd($cleanData);
        return $this->render('data/index.html.twig', [
            'controller_name' => 'DataController',
        ]);
    }

    #[Route('/associations/{department}/{waldec}', name: 'associations')]
    public function associations(int $department, int $waldec): Response
    {
        $filePath = $this->getParameter('kernel.project_dir') . '/src/Data/RNA' . self::VERSION . $department . '.csv';
        $csv = file_get_contents($filePath);
        $data = $this->dataManager->importFromCSV($csv);

        // Add Waldec

        dd($data);
        return $this->render('data/index.html.twig', [
            'controller_name' => 'DataController',
        ]);
    }
}
