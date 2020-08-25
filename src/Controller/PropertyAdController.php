<?php

namespace App\Controller;

use App\Client\GmailClient;
use App\Entity\User;
use App\Enum\PropertyAdFilter;
use App\Exception\ParserNotFoundException;
use App\Repository\PropertyAdRepository;
use App\Service\GoogleOAuthService;
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
     * @param GmailClient        $gmailClient
     * @param GoogleOAuthService $oAuthService
     *
     * @return Response
     */
    public function index(GmailClient $gmailClient, GoogleOAuthService $oAuthService): Response
    {
        /** @var User $user */
        $user = $this->getUser();

        $oAuthService->refreshAccessTokenIfExpired($user);

        $labels = $gmailClient->getLabels($user->getAccessToken());

        $settings = $user->getPropertyAdSearchSettings();

        return $this->render('property_ad/index.html.twig', [
            'gmail_label_choices' => $this->getGmailLabelChoices($labels),
            'newer_than' => $settings[PropertyAdFilter::NEWER_THAN] ?? null,
            'gmail_label' => $settings[PropertyAdFilter::GMAIL_LABEL] ?? null,
            'provider' => $settings[PropertyAdFilter::PROVIDER] ?? null,
            'new_build' => isset($settings[PropertyAdFilter::NEW_BUILD]),
            'sort' => $settings['sort'] ?? null
        ]);
    }

    /**
     * @Route("/list", methods={"GET"}, options={"expose"=true}, name="property_ads_list")
     *
     * @param Request                $request
     * @param PropertyAdRepository   $propertyAdRepository
     * @param PropertyAdSortResolver $sortResolver
     * @param GoogleOAuthService     $oAuthService
     *
     * @return Response
     *
     * @throws ParserNotFoundException
     */
    public function list(
        Request $request,
        PropertyAdRepository $propertyAdRepository,
        PropertyAdSortResolver $sortResolver,
        GoogleOAuthService $oAuthService
    ): Response
    {
        parse_str($request->query->get('filters'), $filters);
        $sort = $request->query->get('sort');

        if (isset($filters[PropertyAdFilter::NEW_BUILD])) {
            $filters[PropertyAdFilter::NEW_BUILD] = (bool) $filters[PropertyAdFilter::NEW_BUILD];
        }

        /** @var User $user */
        $user = $this->getUser();
        $user->setPropertyAdSearchSettings(array_merge($filters, ['sort' => $sort]));
        $this->getDoctrine()->getManager()->flush();

        $oAuthService->refreshAccessTokenIfExpired($user);

        $propertyAds = $propertyAdRepository->find($user->getAccessToken(), $filters);

        return $this->render('property_ad/_list.html.twig', [
            'property_ads' => $propertyAds,
            'sort' => $sortResolver->resolve($sort)
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
