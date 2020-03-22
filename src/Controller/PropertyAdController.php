<?php

namespace App\Controller;

use App\Client\GmailClient;
use App\Entity\User;
use App\Manager\PropertyAdManager;
use App\Exception\ParserNotFoundException;
use App\Service\GoogleService;
use App\Service\PropertyAdSortResolver;
use Google_Service_Gmail_Label;
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
     * @param GmailClient         $gmailClient
     * @param GoogleService       $googleService
     *
     * @return Response
     */
    public function index(
        GmailClient $gmailClient,
        GoogleService $googleService
    ): Response
    {
        /** @var User $user */
        $user = $this->getUser();

        $googleService->refreshAccessTokenIfExpired($user);

        $labels = $gmailClient->getLabels($user->getAccessToken());

        return $this->render('property_ad/index.html.twig', [
            'gmail_label_choices' => $this->getGmailLabelChoices($labels)
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
     * @return Response
     *
     * @throws ParserNotFoundException
     */
    public function list(
        Request $request,
        PropertyAdManager $propertyAdManager,
        PropertyAdSortResolver $sortResolver,
        GoogleService $googleService
    ): Response
    {
        /** @var User $user */
        $user = $this->getUser();

        $googleService->refreshAccessTokenIfExpired($user);

        parse_str($request->query->get('filters'), $filters);

        $propertyAds = $propertyAdManager->find($user->getAccessToken(), $filters);

        return $this->render('property_ad/list.html.twig', [
            'property_ads' => $propertyAds,
            'sort' => $sortResolver->resolve($request->query->get('sort'))
        ]);
    }

    /**
     * @param Google_Service_Gmail_Label[] $labels
     *
     * @return array
     */
    private function getGmailLabelChoices(array $labels): array
    {
        $choices = [];

        foreach ($labels as $label) {
            $choices[$label->getName()] = $label->getId();
        }

        return $choices;
    }
}
