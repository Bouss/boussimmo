<?php

namespace App\Command;

use App\Definition\SiteEnum;
use App\Exception\ParseException;
use App\Parser\OuestFranceImmoParser;
use InvalidArgumentException;
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ParseCommand extends Command
{
    protected static $defaultName = 'app:parse';

    private $logger;

    /**
     * @param LoggerInterface $logger
     */
    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;

        parent::__construct();
    }

    /**
     * {@inheritDoc}
     */
    protected function configure(): void
    {
        $this
            ->setDescription('Extract property ads from a web page')
            ->addArgument('site', InputArgument::REQUIRED, 'Site\'s web page: ' . implode(', ', SiteEnum::getAvailableValues()))
            ->addArgument('file-path', InputArgument::REQUIRED, 'Path of the HTML page')
        ;
    }

    /**
     * {@inheritDoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        try {
            [$site, $file] = $this->getInputs($input);
        } catch (InvalidArgumentException $e) {
            $output->writeln(sprintf('<error>%s</error>', $e->getMessage()));
            exit;
        }

        switch ($site) {
            case SiteEnum::OUESTFRANCE_IMMO:
                $parser = new OuestFranceImmoParser($this->logger);
                break;
            default:
                $output->writeln("<error>No parser found for the site: $site</error>");
                exit;
        }

        $output->writeln('Parsing in progress...');

        try {
            $ads = $parser->parse($file);
        } catch (ParseException $e) {
            $output->writeln(sprintf('<error>Error while parsing: %s</error>', $e->getMessage()));
            exit;
        }

        $table = new Table($output);
        $rows = [];
        $table->setHeaders(['Site', 'External ID', 'Price', 'Area', 'Number of rooms', 'Location', 'Published at', 'Title']);
        foreach ($ads as $ad) {
            $rows[] = [
                $ad->getSite(),
                $ad->getExternalId(),
                $ad->getPrice(),
                $ad->getArea(),
                $ad->getRoomsCount(),
                $ad->getLocation(),
                (null !== $publishedAt = $ad->getPublishedAt()) ? $publishedAt->format('Y-m-d H:i:s') : null,
                $ad->getTitle()
            ];
        }
        $table->setRows($rows);
        $table->render();

        $output->writeln('<info>File parsed with success</info>');
    }

    /**
     * @param InputInterface $input
     *
     * @return string[]
     */
    private function getInputs(InputInterface $input): array
    {
        $site = $input->getArgument('site');
        $filePath = $input->getArgument('file-path');

        SiteEnum::check($site);
        $file = file_get_contents($filePath);

        if (!$file) {
            throw new InvalidArgumentException("Impossible to read the file at: $filePath");
        }

        return [$site, $file];
    }
}