<?php

namespace App\Controller;

use App\Enum\PropertyType;
use App\Enum\Provider;
use App\Factory\ProviderUrlFactory;
use App\Formatter\DecimalFormatter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;

/**
 * @Route("/provider")
 */
class ProviderController extends AbstractController
{
    /**
     * @Route("/result-urls", methods={"GET"}, options={"expose"=true}, name="provider_result_urls")
     *
     * @param Request             $request
     * @param SerializerInterface $serializer
     * @param ProviderUrlFactory  $urlFactory
     * @param DecimalFormatter    $formatter
     *
     * @return JsonResponse
     */
    public function getResultUrls(
        Request $request,
        SerializerInterface $serializer,
        ProviderUrlFactory $urlFactory,
        DecimalFormatter $formatter
    ): JsonResponse
    {
        $params = $request->query->all();

        $city = $params['city'];
        $types = isset($params['types']) ? array_keys($params['types']) : PropertyType::getAvailableValues();
        $minPrice = isset($params['min_price']) ? $formatter->parse($params['min_price']) : null;
        $maxPrice = $formatter->parse($params['max_price']);
        $minArea = isset($params['min_area']) ? $formatter->parse($params['min_area']) : null;
        $maxArea = isset($params['max_area']) ? $formatter->parse($params['max_area']) : null;
        $minRoomsCount = $params['min_rooms_count'];
        $urls = [];

        foreach (Provider::getAvailableValues() as $providerId) {
            $urls[] = $serializer->normalize($urlFactory->create(
                $providerId,
                $city,
                $types,
                $minPrice,
                $maxPrice,
                $minArea,
                $maxArea,
                $minRoomsCount
            ));
        }

        return $this->json($urls);
    }
}
