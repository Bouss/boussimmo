<?php

namespace App\Controller;

use App\Client\GmailClient;
use App\Entity\User;
use App\Form\Type\FilterPropertyAdsType;
use App\Form\Type\SortPropertyAdsType;
use App\Manager\PropertyAdManager;
use App\Exception\ParserNotFoundException;
use App\Service\GoogleService;
use App\Service\PropertyAdSortResolver;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;

/**
 * @Route("/property-ads")
 */
class PropertyAdController extends AbstractController
{
    /**
     * @param Request             $request
     * @param SerializerInterface $serializer
     * @param GmailClient         $gmailClient
     * @param GoogleService       $googleService
     *
     * @return Response
     */
    public function index(
        Request $request,
        SerializerInterface $serializer,
        GmailClient $gmailClient,
        GoogleService $googleService
    ): Response
    {
        /** @var User $user */
        $user = $this->getUser();

        $googleService->refreshAccessTokenIfExpired($user);

        $labels = $gmailClient->getLabels($user->getAccessToken());

        $data = [
//            'newerThan' => $filters['newer_than'],
//            'label' => $filters['label'],
//            'newBuild' => $filters['new_build']
        ];
        $sort = '';

        $filterForm = $this->createForm(FilterPropertyAdsType::class, $data, [
            'labels' => $labels
        ]);
        
        $sortForm = $this->createForm(SortPropertyAdsType::class, ['sort' => $sort]);

        return $this->render('property_ad/index.html.twig', [
            'filter_form' => $filterForm->createView(),
            'sort_form' => $sortForm->createView()
        ]);
    }

    /**
     * @Route("/list", methods={"GET"}, options={"expose"=true}, name="property_ads_list")
     *
     * @param Request                $request
     * @param PropertyAdManager      $propertyAdManager
     * @param PropertyAdSortResolver $sortResolver
     * @param GoogleService          $googleService
     *
     * @return JsonResponse
     *
     * @throws ParserNotFoundException
     */
    public function list(
        Request $request,
        PropertyAdManager $propertyAdManager,
        PropertyAdSortResolver $sortResolver,
        GoogleService $googleService
    ): JsonResponse
    {
        /** @var User $user */
        $user = $this->getUser();

        $googleService->refreshAccessTokenIfExpired($user);

        parse_str($request->query->get('filters'), $filters);
        $newerThan = $filters['filter_property_ads']['newerThan'];
        $label = $filters['filter_property_ads']['label'];
        $isNewBuild = isset($filters['filter_property_ads']['newBuild']);


        $propertyAds = $propertyAdManager->find($user->getAccessToken(), $newerThan, $label, $isNewBuild);

        return new JsonResponse([
            'html' => $this->renderView('property_ad/_property_ad_container.html.twig', [
                'property_ads' => $propertyAds,
                'sort' => $sortResolver->resolve($request->query->get('sort'))
            ]),
            'property_ad_count' => count($propertyAds)
        ]);
    }
}
