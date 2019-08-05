<?php

namespace App\Command;

use App\Definition\CityEnum;
use App\Definition\SiteEnum;
use App\Entity\PropertyType;
use App\Exception\AccessDeniedException;
use App\Exception\ParseException;
use App\Exception\ScraperLocatorException;
use App\ScraperContainer;
use InvalidArgumentException;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class ScrapCommand extends Command
{
    protected static $defaultName = 'site:scrap';

    /**
     * @var ScraperContainer
     */
    private $scraperContainer;

    /**
     * @param ScraperContainer $scraperContainer
     */
    public function __construct(ScraperContainer $scraperContainer)
    {
        $this->scraperContainer = $scraperContainer;

        parent::__construct();
    }

    /**
     * {@inheritDoc}
     */
    protected function configure(): void
    {
        $this
            ->setDescription('Extract property ads from a web page')
            ->addArgument('site', InputArgument::REQUIRED, 'Site: ' . implode(', ', SiteEnum::getAvailableValues()))
            ->addArgument('city', InputArgument::REQUIRED, 'City: ' . implode(', ', CityEnum::getAvailableValues()))
            ->addArgument('property-type', InputArgument::REQUIRED, 'Property type: ' . implode(', ', PropertyType::AVAILABLE_TYPES))
            ->addOption('min-price', null, InputOption::VALUE_OPTIONAL, 'Minimum price')
            ->addOption('max-price', 'p', InputOption::VALUE_REQUIRED, 'Maximum price')
            ->addOption('min-area', 'a', InputOption::VALUE_REQUIRED, 'Minimum area')
            ->addOption('max-area', null, InputOption::VALUE_OPTIONAL, 'Maximum area')
            ->addOption('min-rooms-count', 'r', InputOption::VALUE_REQUIRED, 'Minimum number of rooms')
            ->addOption('max-rooms-count', null, InputOption::VALUE_OPTIONAL, 'Maximum number of rooms')
        ;
    }

    /**
     * {@inheritDoc}
     *
     * @throws ScraperLocatorException
     * @throws AccessDeniedException
     * @throws ParseException
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        [$site, $city, $propertyType, $minPrice, $maxPrice, $minArea, $maxArea, $minRoomsCount, $maxRoomsCount] = $this->getInputs($input);

        $scraper = $this->scraperContainer->get($site);

        $io = new SymfonyStyle($input, $output);
        $io->title($site . ' scraping');
        $io->section('Search criteria');
        $io->listing([
            '<info>City</info>: ' . $city,
            '<info>Minimum price</info>: ' . $minPrice,
            '<info>Maximum price</info>: ' . $maxPrice,
            '<info>Minimum area</info>: ' . $minArea,
            '<info>Maximum area</info>: ' . $maxArea,
            '<info>Minimum number of rooms</info>: ' . $minRoomsCount,
            '<info>Maximum number of rooms</info>: ' . $maxRoomsCount,
        ]);

        $io->writeln('<comment>Scraping in progress...</comment>');
        $ads = $scraper->scrap($city, $propertyType, $minPrice, $maxPrice, $minArea, $maxArea, $minRoomsCount, $maxRoomsCount);

        $io->section('Results');
        $rows = [];
        foreach ($ads as $ad) {
            $rows[] = [
                $ad->getSite(),
                $ad->getExternalId(),
                $ad->getPrice(),
                $ad->getArea(),
                $ad->getRoomsCount(),
                $ad->getLocation(),
                (null !== $publishedAt = $ad->getPublishedAt()) ? $publishedAt->format('Y-m-d H:i:s') : null,
                $ad->isNewBuild() ? 'New-build' : '',
                substr($ad->getUrl(), 0, 50) . '...',
            ];
        }

        $io->table(
            ['Site', 'External ID', 'Price', 'Area', 'Number of rooms', 'Location', 'Publication date', 'New-build', 'URL'],
            $rows
        );

        $io->success('Results found: ' . count($ads));
    }

    /**
     * @param InputInterface $input
     *
     * @return array
     */
    private function getInputs(InputInterface $input): array
    {
        $site = $input->getArgument('site');
        $city = $input->getArgument('city');
        $propertyType = $input->getArgument('property-type');
        $minPrice = $input->getOption('min-price');
        $maxPrice = $input->getOption('max-price');
        $minArea = $input->getOption('min-area');
        $maxArea = $input->getOption('max-area');
        $minRoomsCount = $input->getOption('min-rooms-count');
        $maxRoomsCount = $input->getOption('max-rooms-count');

        SiteEnum::check($site);
        CityEnum::check($city);

        if (!in_array($propertyType, PropertyType::AVAILABLE_TYPES)) {
            throw new InvalidArgumentException(sprintf('Invalid property type: "%s". Available property types are: %s', $propertyType, PropertyType::AVAILABLE_TYPES));
        }

        return [$site, $city, $propertyType, $minPrice, $maxPrice, $minArea, $maxArea, $minRoomsCount, $maxRoomsCount];
    }
}
