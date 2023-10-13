<?php

declare(strict_types=1);

namespace KaamelottGifboard\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Finder\Finder;

class CleanTweetedGifCommand extends Command
{
    private const MAX_DAYS_BEFORE_DELETION = 14;

    protected static $defaultName = 'tweet:clean:posted';

    public function __construct(
        private string $publicPath,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->setDescription('Clean the recently posted GIF to @KGifboard')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $deleted = 0;

        $postedGifs = (new Finder())->files()->in($this->publicPath.'/twitter');

        foreach ($postedGifs as $postedGif) {
            $tweetCreatedAt = (new \DateTime())->setTimestamp($postedGif->getCTime());

            // Gif posted more than two weeks ago.
            if ($tweetCreatedAt->diff(new \DateTime())->days > self::MAX_DAYS_BEFORE_DELETION) {
                unlink($postedGif->getPathname());

                ++$deleted;
            }
        }

        $output->writeln(sprintf('%d GIF has been deleted', $deleted));

        return Command::SUCCESS;
    }
}
