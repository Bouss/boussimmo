<?php

namespace App\Scraper;

use App\Entity\PropertyAd;
use App\Exception\AccessDeniedException;
use App\Exception\ParseException;
use App\Parser\AbstractParser;
use App\Parser\WebParser\AbstractWebParser;
use App\UrlBuilder\AbstractUrlBuilder;
use App\Util\StringUtil;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Panther\Client;

abstract class AbstractScraper
{
    private const ACCESS_DENIED_WORDS = ['access denied', 'permission to access'];

    /**
     * @var AbstractUrlBuilder
     */
    private $urlBuilder;

    /**
     * @var AbstractParser
     */
    private $parser;

    /**
     * @param AbstractUrlBuilder $urlBuilder
     * @param AbstractWebParser  $parser
     */
    public function __construct(AbstractUrlBuilder $urlBuilder, AbstractWebParser $parser)
    {
        $this->urlBuilder = $urlBuilder;
        $this->parser = $parser;
    }

    /**
     * @param string   $city
     * @param int      $propertyType
     * @param int|null $minPrice
     * @param int      $maxPrice
     * @param int      $minArea
     * @param int|null $maxArea
     * @param int      $minRoomsCount
     * @param int|null $maxRoomsCount
     *
     * @return PropertyAd[]
     *
     * @throws AccessDeniedException
     * @throws ParseException
     */
    public function scrap(
        string $city,
        int $propertyType,
        ?int $minPrice,
        int $maxPrice,
        int $minArea,
        ?int $maxArea,
        int $minRoomsCount,
        ?int $maxRoomsCount
    ): array
    {
        $url = $this->urlBuilder->buildUrl(...func_get_args());

        $client = Client::createChromeClient();
        $client->request('GET', $url);
        
        // Manage a "403 Access Denied" response or equivalent
        $response = $client->getInternalResponse();
        if (
            Response::HTTP_FORBIDDEN === $response->getStatusCode() ||
            StringUtil::contains($response->getContent(), self::ACCESS_DENIED_WORDS)
        ) {
            throw new AccessDeniedException(sprintf('Access denied for site: "%s" with URL: "%s"', $this->urlBuilder->getSite(), $url));
        }

        $html = $client->getPageSource();

        // Kill the client in order to avoid "The port is already in use" error when the client is needed again
        unset($client);

        return $this->parser->parse($html);
    }
}
