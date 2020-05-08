<?php

namespace App\Controller;

use App\Enum\Provider;
use App\Exception\ParserNotFoundException;
use App\Repository\ProviderRepository;
use App\UrlBuilderContainer;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/provider")
 */
class ProviderController extends AbstractController
{
    /**
     * @Route("/search-urls", methods={"POST"}, options={"expose"=true}, name="provider_search_urls")
     *
     * @param Request             $request
     * @param UrlBuilderContainer $urlBuilderContainer
     * @param ProviderRepository  $providerRepository
     *
     * @return JsonResponse
     *
     * @throws ParserNotFoundException
     */
    public function getSearchUrls(
        Request $request,
        UrlBuilderContainer $urlBuilderContainer,
        ProviderRepository $providerRepository
    ): JsonResponse
    {
        $params = $request->request;
        $city = $params->get('city');
        $types = array_keys($params->get('types'));
        $minPrice = $params->get('min_price') ?: null;
        $maxPrice = $params->get('max_price');
        $minArea = $params->get('min_area');
        $maxArea = $params->get('max_area') ?: null;
        $minRoomsCount = $params->get('min_rooms_count');
        $data = [];

        foreach (Provider::getAvailableValues() as $id) {
            $provider = $providerRepository->find($id);

            if (null === $provider) {
                continue;
            }

            $urlBuilder = $urlBuilderContainer->get($id);

            $data[] = [
                'provider' => $provider->getId(),
                'logo' => $provider->getLogo(),
                'url' => $urlBuilder->build($city, $types, $minPrice, $maxPrice, $minArea, $maxArea, $minRoomsCount)
            ];
        }

        return $this->json($data);
    }
}