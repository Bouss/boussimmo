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
    private const ORDER_ASC = 1;

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

    /**
     * @param Property[] $properties
     *
     * @return Property[]
     */
    public function sortBy(array $properties, string $field, int $order = self::ORDER_ASC): array
    {
        $getter = 'get' . ucfirst($field);

        if (!method_exists(PropertyAd::class, $getter)) {
            return $properties;
        }

        usort($properties, static function (Property $p1, Property $p2) use ($getter, $order) {
            $comparison = $p1->{$getter}() <=> $p2->{$getter}();

            return self::ORDER_ASC === $order ? $comparison : -$comparison;
        });

        return $properties;
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
