<?php

namespace App\Controller;

use App\Client\GmailClient;
use App\Entity\User;
use App\Enum\PropertyAdFilter;
use App\Exception\GoogleTokenRevokedException;
use App\Exception\ParserNotFoundException;
use App\Repository\PropertyAdRepository;
use App\Service\GoogleOAuthService;
use App\Service\PropertyAdSortResolver;
use Google_Service_Gmail_Label;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/property-ads")
 */
class PropertyAdController extends AbstractController
{
    private GmailClient $gmailClient;
    private GoogleOAuthService $oAuthService;
    private PropertyAdRepository $propertyAdRepository;
    private PropertyAdSortResolver $sortResolver;
    private LoggerInterface $logger;

    public function __construct(
        GmailClient $gmailClient,
        GoogleOAuthService $oAuthService,
        PropertyAdRepository $propertyAdRepository,
        PropertyAdSortResolver $sortResolver,
        LoggerInterface $logger
    ) {
        $this->gmailClient = $gmailClient;
        $this->oAuthService = $oAuthService;
        $this->propertyAdRepository = $propertyAdRepository;
        $this->sortResolver = $sortResolver;
        $this->logger = $logger;
    }

    public function index(): Response
    {
        /** @var User $user */
        $user = $this->getUser();

        try {
            $this->oAuthService->refreshAccessTokenIfExpired($user);
            $labels = $this->gmailClient->getLabels($user->getAccessToken());
        } catch (GoogleTokenRevokedException $e) {
            $this->logger->error($e->getMessage());

            return $this->redirectToRoute('app_logout');
        }

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

     * @throws ParserNotFoundException
     */
    public function list(Request $request): Response
    {
        parse_str($request->query->get('filters'), $filters);
        $sort = $request->query->get('sort');

        if (isset($filters[PropertyAdFilter::NEW_BUILD])) {
            $filters[PropertyAdFilter::NEW_BUILD] = true;
        }

        /** @var User $user */
        $user = $this->getUser();
        $user->setPropertyAdSearchSettings(array_merge($filters, ['sort' => $sort]));
        $this->getDoctrine()->getManager()->flush();

        try {
            $this->oAuthService->refreshAccessTokenIfExpired($user);
        } catch (GoogleTokenRevokedException $e) {
            return $this->redirectToRoute('app_logout');
        }

        $propertyAds = $this->propertyAdRepository->find($user->getAccessToken(), $filters);

        return $this->render('property_ad/_list.html.twig', [
            'property_ads' => $propertyAds,
            'sort' => $this->sortResolver->resolve($sort)
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
