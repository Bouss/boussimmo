<?php

namespace App\Controller;

use App\Exception\MailboxConnectionException;
use App\Exception\ParserNotFoundException;
use App\Manager\PropertyAdManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/property-ads")
 */
class PropertyAdController extends AbstractController
{
    /**
     * @Route("/", methods={"GET"}, name="property_ad_index")
     *
     * @param Request           $request
     * @param PropertyAdManager $propertyAdManager
     *
     * @return Response
     * @throws MailboxConnectionException
     * @throws ParserNotFoundException
     */
    public function index(Request $request, PropertyAdManager $propertyAdManager): Response
    {
        $propertyAds = $propertyAdManager->find();

        return $this->render('property_ad/index.html.twig', [
            'property_ads' => $propertyAds
        ]);
    }
}
