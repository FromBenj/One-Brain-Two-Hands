<?php

namespace App\Service;

use AllowDynamicProperties;
use Symfony\Contracts\HttpClient\HttpClientInterface;

#[AllowDynamicProperties]
class UserManager
{
    public function __construct(HttpClientInterface $client, AssociationManager $associationManager)
    {
        $this->client = $client;
        $this->associationManager = $associationManager;
    }

    function getUserDepartment(array $coordinates): ?int
    {
        $lat = $coordinates['lat'];
        $lon = $coordinates['lon'];
        $endpoint = "https://geo.api.gouv.fr/communes?lat=" . $lat . "&lon=" . $lon . "&fields=code";
        $response = $this->client->request('GET', $endpoint);
        $data = $response->toArray();
        $cityData = $data[0]['code'];
        $department = substr($cityData, 0, 2);
        if (in_array((int)$department, $this->associationManager->setDepartments(), true)) {

            return (int)$department;
        }

        return null;
    }

}
