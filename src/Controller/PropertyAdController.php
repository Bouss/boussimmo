<?php

namespace App\Controller;

use App\Form\Type\FilterPropertyAdsType;
use App\Form\Type\SortPropertyAdsType;
use App\Manager\PropertyAdManager;
use App\Exception\ParserNotFoundException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;

/**
 * @Route("/property-ads")
 */
class PropertyAdController extends AbstractController
{
    /**
     * @Route("/", methods={"GET"}, options={"expose"=true}, name="property_ad_index")
     *
     * @param Request             $request
     * @param SerializerInterface $serializer
     *
     * @return Response
     */
    public function index(Request $request, SerializerInterface $serializer): Response
    {
        $filters = json_decode($request->cookies->get('filters'), true);
        $sort = json_decode($request->cookies->get('sort'), true);

        $data = [
            'newerThan' => $filters['newer_than'],
            'label' => $filters['label'],
            'newBuild' => $filters['new_build']
        ];

        $filterForm = $this->createForm(FilterPropertyAdsType::class, $data, [
            'labels' => $serializer->denormalize($request->get('labels'), 'App\Model\GmailLabel[]')
        ]);
        
        $sortForm = $this->createForm(SortPropertyAdsType::class, ['sort' => $sort]);

        return $this->render('property_ad/_index.html.twig', [
            'profile_image' => $request->query->get('profile_image'),
            'email' => $request->query->get('email'),
            'filter_form' => $filterForm->createView(),
            'sort_form' => $sortForm->createView()
        ]);
    }

    /**
     * @Route("/list", methods={"POST"}, options={"expose"=true}, name="property_ads_list")
     *
     * @param Request           $request
     * @param PropertyAdManager $propertyAdManager
     *
     * @return JsonResponse
     *
     * @throws ParserNotFoundException
     */
    public function list(Request $request, PropertyAdManager $propertyAdManager): JsonResponse
    {
        // Without the "X-Requested-With" header, this request could be forged: could be a CSRF attack. Abort.
        if (null === $request->headers->get('X-Requested-With')) {
            throw new AccessDeniedHttpException();
        }

        $filters = $request->query->get('filters');
        $filters['new_build'] = filter_var($filters['new_build'], FILTER_VALIDATE_BOOLEAN);

        $propertyAds = $propertyAdManager->find($request->getContent(), $filters);

        return new JsonResponse([
            'html' => $this->renderView('property_ad/_property_ad_container.html.twig', [
                'property_ads' => $propertyAds,
                'sort' => json_decode($request->query->get('sort'), true)
            ]),
            'property_ad_count' => count($propertyAds)
        ]);
    }
}
