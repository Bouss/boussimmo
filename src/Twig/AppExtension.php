<?php

namespace App\Twig;

use App\DataProvider\ProviderProvider;
use App\DTO\Property;
use App\DTO\PropertyAd;
use DateTime;
use DateTimeZone;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class AppExtension extends AbstractExtension
{
    private const DEFAULT_TIMEZONE = 'Europe/Paris';

    public function __construct(
        private ProviderProvider $providerProvider
    ) {}

    /**
     * {@inheritDoc}
     */
    public function getFilters(): array
    {
        return [
            new TwigFilter('provider_logo', [$this, 'getProviderLogo']),
            new TwigFilter('sort_by', [$this, 'sortBy']),
            new TwigFilter('days_ago', [$this, 'getDaysAgo'])
        ];
    }

    public function getProviderLogo(PropertyAd $propertyAd): ?string
    {
        $provider = $this->providerProvider->find($propertyAd->getProvider());

        return null !== $provider ? $provider->getLogo() : null;
    }

    public function getDaysAgo(DateTime $date): string
    {
        $timezone = new DateTimeZone(self::DEFAULT_TIMEZONE);

        $daysDiff = (new DateTime('now', $timezone))->setTime(0, 0)
            ->diff((clone $date)->setTimezone($timezone)->setTime(0, 0))
            ->days;

        if (0 === $daysDiff) {
            return (clone $date)->setTimezone($timezone)->format('H:i:s');
        }
        if (1 === $daysDiff) {
            return 'Hier';
        }
        if ($daysDiff >= 2 && $daysDiff <= 6) {
            return "Il y a $daysDiff jours";
        }
        if (7 === $daysDiff) {
            return 'Il y a 1 semaine';
        }

        return $date->format('d/m/Y');
    }
}
