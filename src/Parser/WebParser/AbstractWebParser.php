<?php

namespace App\Parser\WebParser;

use App\Entity\PropertyAd;
use App\Exception\ParseException;
use App\Parser\AbstractParser;
use Exception;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Component\Panther\Client;

abstract class AbstractWebParser extends AbstractParser
{
    protected const SELECTOR_NEXT_PAGE_URL = '';

    /**
     * {@inheritDoc}
     */
    public function parse(string $html, array $options = []): array
    {
        $client = Client::createChromeClient();
        $crawler = new Crawler($html);

        do {
            try {
                $crawler->filter(static::SELECTOR_AD_WRAPPER);
            } catch (Exception $e) {
                throw new ParseException('No property ads found: ' . $e->getMessage());
            }

            // Iterate over all DOM elements wrapping a property ad on the current page
            /** @var PropertyAd[] $ads */
            $ads[] = $crawler->filter(static::SELECTOR_AD_WRAPPER)->each(function (Crawler $adCrawler) {
                try {
                    return $this->buildPropertyAd($adCrawler);
                } catch (Exception $e) {
                    $this->logger->error('Error while parsing a property ad: ' . $e->getMessage(), ['site' => static::SITE]);

                    return null;
                }
            });

            // Fetch the next page
            $nextPage = $this->getNextPageUrl($crawler);
            if (null !== $nextPage) {
                $client->request('GET', $nextPage);
                $crawler = new Crawler($client->getPageSource());
            }

        } while (null !== $nextPage);

        unset($client);

        // Merge all the ad arrays in one and clean the ads (remove null values)
        $ads = array_filter(array_merge(...$ads), static function (?PropertyAd $ad) {
            return null !== $ad;
        });

        return $ads;
    }

    /**
     * @param Crawler $crawler
     *
     * @return string|null
     */
    protected function getNextPageUrl(Crawler $crawler): ?string
    {
        if (empty(static::SELECTOR_NEXT_PAGE_URL)) {
            return null;
        }

        try {
            return $crawler->filter(static::SELECTOR_NEXT_PAGE_URL)->attr('href');
        } catch (Exception $e) {
            return null;
        }
    }
}
