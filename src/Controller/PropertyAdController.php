<?php

namespace App\Controller;

use App\Client\GmailClient;
use App\Entity\User;
use App\Enum\PropertyAdFilter;
use App\Exception\ParserNotFoundException;
use App\Repository\PropertyAdRepository;
use App\Service\GoogleService;
use App\Service\PropertyAdSortResolver;
use Doctrine\ORM\EntityManagerInterface;
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
     * @param GmailClient   $gmailClient
     * @param GoogleService $googleService
     *
     * @return Response
     */
    public function index(GmailClient $gmailClient, GoogleService $googleService): Response
    {
        /** @var User $user */
        $user = $this->getUser();

        $googleService->refreshAccessTokenIfExpired($user);

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
     * @param EntityManagerInterface $em
     * @param PropertyAdRepository   $propertyAdRepository
     * @param PropertyAdSortResolver $sortResolver
     * @param GoogleService          $googleService
     *
     * @return Response
     *
     * @throws ParserNotFoundException
     */
    public function list(
        Request $request,
        EntityManagerInterface $em,
        PropertyAdRepository $propertyAdRepository,
        PropertyAdSortResolver $sortResolver,
        GoogleService $googleService
    ): Response
    {
        parse_str($request->query->get('filters'), $filters);
        $sort = $request->query->get('sort');

        /** @var User $user */
        $user = $this->getUser();
        $user->setPropertyAdSearchSettings(array_merge($filters, ['sort' => $sort]));
        $em->flush();

        $googleService->refreshAccessTokenIfExpired($user);

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
