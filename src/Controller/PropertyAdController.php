<?php

namespace App\Controller;

use App\Manager\PropertyAdManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/property-ads")
 */
class PropertyAdController extends AbstractController
{
    /**
     * @Route("/", methods={"GET"}, name="property_ad_index")
     *
     * @return Response
     */
    public function index(): Response
    {
        return $this->render('property_ad/index.html.twig');
    }

    /**
     * @Route("/list", methods={"POST"}, options={"expose"=true}, name="property_ads_list")
     *
     * @param Request           $request
     * @param PropertyAdManager $propertyAdManager
     *
     * @return Response
     */
    public function list(Request $request, PropertyAdManager $propertyAdManager): Response
    {
        // Without the "X-Requested-With" header, this request could be forged: could be a CSRF attack. Abort.
        if (null === $request->headers->get('X-Requested-With')) {
            throw new AccessDeniedHttpException();
        }

        $userToken = $request->getContent();

        $propertyAds = $propertyAdManager->find($userToken);

        return $this->render('property_ad/_property_ad_container.html.twig', [
            'property_ads' => $propertyAds
        ]);
    }
}
