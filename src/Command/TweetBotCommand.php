<?php

declare(strict_types=1);

namespace KaamelottGifboard\Command;

use Abraham\TwitterOAuth\TwitterOAuth;
use KaamelottGifboard\DataObject\Gif;
use KaamelottGifboard\Service\GifFinder;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\HttpFoundation\Response;

class TweetBotCommand extends Command
{
    private const QUOTE_MAX_LENGTH = 200;
    private const MAX_TRIES = 10;
    private const POSTED_PATH = '%s/twitter/%s.txt';

    protected static $defaultName = 'tweet:random';

    public function __construct(
        private GifFinder $gifFinder,
        private string $publicPath,
        private string $twitterApiKey,
        private string $twitterApiKeySecret,
        private string $twitterAccessToken,
        private string $twitterAccessTokenSecret,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->setDescription('Tweet a random GIF to @KGifboard')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $nbTry = 0;

        do {
            ++$nbTry;
            $random = $this->gifFinder->findRandom()['current'];
        } while ($this->isRecentlyPosted($random, $output) && $nbTry < self::MAX_TRIES);

        if ($nbTry >= self::MAX_TRIES) {
            $output->writeln(sprintf('Cannot find a GIF not recently posted in %s tries, aborting.', $nbTry));

            return Command::FAILURE;
        }

        file_put_contents($this->getPostedGifPath($random), ''); // Mark this as GIF

        $quote = $random->quote;

        if (strlen($quote) > self::QUOTE_MAX_LENGTH) {
            $quote = substr($quote, 0, self::QUOTE_MAX_LENGTH).'...';
        }

        $connection = $this->getTwitterOAuth();
        $connection->setApiVersion('1.1');

        try {
            /** @var object $media */
            $media = $connection->upload('media/upload', ['media' => $this->publicPath.'/gifs/'.$random->filename]);
        } catch (\Throwable $e) {
            $output->writeln(sprintf('Error while uploading the file to Twitter [%s]', $e->getMessage()));

            return Command::FAILURE;
        }

        if (!property_exists($media, 'media_id_string')) {
            $output->writeln('Media for GIF %s not uploaded on Twitter.', $random->filename);

            return Command::FAILURE;
        }

        $connection->setApiVersion('2');

        $tweet = sprintf(
            '%s #%s #kaamelott #citationDuJour %s',
            $quote,
            preg_replace('/[\s\']/', '', $random->charactersSpeaking[0]->name),
            $random->shortUrl
        );

        /** @var object $response */
        $response = $connection->post('tweets', [
            'text' => $tweet,
            'media' => ['media_ids' => [$media->media_id_string]],
        ], true);

        if (Response::HTTP_CREATED !== $connection->getLastHttpCode()) {
            $output->writeln(sprintf(
                'Tweet for GIF %s not published on Twitter. [%s]',
                $random->filename,
                $response->errors[0]->message /* @phpstan-ignore-line */
            ));

            return Command::FAILURE;
        }

        $output->writeln(sprintf('[%s] Posted on Twitter => %s', (new \DateTime())->format('Y-m-d H:i:s'), $tweet));

        return Command::SUCCESS;
    }

    private function getTwitterOAuth(): TwitterOAuth
    {
        return new TwitterOAuth(
            $this->twitterApiKey,
            $this->twitterApiKeySecret,
            $this->twitterAccessToken,
            $this->twitterAccessTokenSecret,
        );
    }

    private function isRecentlyPosted(Gif $gif, OutputInterface $output): bool
    {
        if (file_exists($this->getPostedGifPath($gif))) {
            $output->writeln(sprintf('GIF %s has been recently posted.', $gif->image));

            return true;
        }

        return false;
    }

    private function getPostedGifPath(Gif $gif): string
    {
        return sprintf(self::POSTED_PATH, $this->publicPath, $gif->filename);
    }
}
