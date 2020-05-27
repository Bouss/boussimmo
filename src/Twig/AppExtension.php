<?php

namespace App\Twig;

use App\DTO\PropertyAd;
use App\Repository\ProviderRepository;
use DateTime;
use Psr\Log\LoggerInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class AppExtension extends AbstractExtension
{
    private const ORDER_ASC = 1;

    private ProviderRepository $providerRepository;

    /**
     * @param ProviderRepository $ProviderRepository
     */
    public function __construct(ProviderRepository $ProviderRepository)
    {
        $this->providerRepository = $ProviderRepository;
    }

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

    /**
     * @param PropertyAd $propertyAd
     *
     * @return string|null
     */
    public function getProviderLogo(PropertyAd $propertyAd): ?string
    {
        $provider = $this->providerRepository->find($propertyAd->getProvider());

        return null !== $provider ? $provider->getLogo() : null;
    }

    /**
     * @param PropertyAd[] $propertyAds
     * @param string       $field
     * @param int          $order
     *
     * @return PropertyAd[]
     */
    public function sortBy(array $propertyAds, string $field, int $order = self::ORDER_ASC): array
    {
        $getter = 'get' . ucfirst($field);

        if (!method_exists(PropertyAd::class, $getter)) {
            return $propertyAds;
        }

        usort($propertyAds, static function (PropertyAd $a1, PropertyAd $a2) use ($getter, $order) {
            $comparison = $a1->{$getter}() <=> $a2->{$getter}();

            return self::ORDER_ASC === $order ? $comparison : -$comparison;
        });

        return $propertyAds;
    }

    /**
     * @param DateTime $date
     *
     * @return string
     */
    public function getDaysAgo(DateTime $date): string
    {
        $dateClone = clone $date;
        $daysDiff = (new DateTime())->setTime(0, 0)->diff($dateClone->setTime(0, 0))->days;

        if (0 === $daysDiff) {
            return 'Aujourd\'hui';
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
