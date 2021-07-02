<?php

namespace App\Controller;

use App\Enum\PropertyType;
use App\Enum\Provider;
use App\Exception\UrlBuilderNotFoundException;
use App\Factory\ProviderUrlFactory;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

#[Route("/provider")]
class ProviderController extends AbstractController
{
    public function __construct(
        private ProviderUrlFactory $urlFactory
    ) {}

    #[Route("/result-urls", name: "provider_result_urls", options: ["expose" => true], methods: ["GET"])]
    public function getResultUrls(Request $request): JsonResponse
    {
        $params = $request->query->all();
        $city = $params['city'];
        $types = isset($params['types']) ? array_keys($params['types']) : PropertyType::getAvailableValues();
        $minPrice = $params['min_price'] ?? null;
        $maxPrice = $params['max_price'];
        $minArea = $params['min_area'] ?? null;
        $maxArea = $params['max_area'] ?? null;
        $minRoomsCount = $params['min_rooms_count'];
        $urls = [];

        foreach (Provider::getAvailableValues() as $providerName) {
            try {
                $urls[] = $this->urlFactory->create($providerName, $city, $types, $minPrice, $maxPrice, $minArea, $maxArea, $minRoomsCount);
            // TODO: process ParuVendu
            } catch (UrlBuilderNotFoundException) {
                continue;
            }
        }

        return $this->json($urls);
    }
}
