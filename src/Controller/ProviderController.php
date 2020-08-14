<?php

namespace App\Controller;

use App\Enum\PropertyType;
use App\Enum\Provider;
use App\Exception\ParserNotFoundException;
use App\Factory\ProviderUrlFactory;
use App\Formatter\DecimalFormatter;
use App\Repository\ProviderRepository;
use App\UrlBuilderContainer;
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
     * @Route("/result-urls", methods={"POST"}, options={"expose"=true}, name="provider_result_urls")
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
        $params = $request->request;
        $city = $params->get('city');
        $types = null !== $params->get('types') ? array_keys($params->get('types')) : PropertyType::getAvailableValues();
        $minPrice = '' !== $params->get('min_price') ? $formatter->parse($params->get('min_price')) : null;
        $maxPrice = $formatter->parse($params->get('max_price'));
        $minArea = '' !== $params->get('min_area') ? $formatter->parse($params->get('min_area')) : null;
        $maxArea = '' !== $params->get('max_area') ? $formatter->parse($params->get('max_area')) : null;
        $minRoomsCount = $params->get('min_rooms_count');
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
