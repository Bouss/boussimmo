<?php

namespace App\Controller;

use App\Manager\PropertyAdManager;
use App\Exception\ParserNotFoundException;
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
     * @Route("/", methods={"GET"}, options={"expose"=true}, name="property_ad_index")
     *
     * @param Request $request
     *
     * @return Response
     */
    public function index(Request $request): Response
    {
        return $this->render('property_ad/_index.html.twig', [
            'profile_image' => $request->query->get('profile_image'),
            'email' => $request->query->get('email')
        ]);
    }

    /**
     * @Route("/list", methods={"POST"}, options={"expose"=true}, name="property_ads_list")
     *
     * @param Request           $request
     * @param PropertyAdManager $propertyAdManager
     *
     * @return Response
     *
     * @throws ParserNotFoundException
     */
    public function list(Request $request, PropertyAdManager $propertyAdManager): Response
    {
        // Without the "X-Requested-With" header, this request could be forged: could be a CSRF attack. Abort.
        if (null === $request->headers->get('X-Requested-With')) {
            throw new AccessDeniedHttpException();
        }

        $propertyAds = $propertyAdManager->find(
            $request->request->get('access_token'),
            $request->request->get('newer_than'),
            $request->request->get('labels', [])
        );

        return $this->render('property_ad/_property_ad_container.html.twig', [
            'property_ads' => $propertyAds
        ]);
    }
}
