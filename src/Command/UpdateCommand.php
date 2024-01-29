<?php

declare(strict_types=1);

namespace KaamelottGifboard\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Serializer\Encoder\DecoderInterface;
use Symfony\Component\String\Slugger\SluggerInterface;

class UpdateCommand extends Command
{
    protected static $defaultName = 'update:gifs';

    public function __construct(
        private DecoderInterface $decoder,
        private string $gifsJsonFile,
        private string $redirectionJsonFile,
        private SluggerInterface $slugger,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->setDescription('Update GIFs via CSV.')
            ->setHelp('This will update existing GIFs to the JSON file based on a CSV.')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        try {
            $file = \file_get_contents(__DIR__.'/gifs.csv');
        } catch (\Throwable) {
            $output->writeln('Error: Could not find the "gifs.csv". Aborting');

            return Command::FAILURE;
        }

        $currentGifs = $this->getCurrentGifs();

        /** @var array $gifsFromCsv */
        $gifsFromCsv = $this->decoder->decode((string) $file, 'csv');

        $gifs = [];
        $warnings = [];
        $redirections = [];

        /** @var array<string> $gif */
        foreach ($gifsFromCsv as $gif) {
            $filename = sprintf('%s.gif', $gif['Filename']);

            /** @var ?array $currentGif */
            $currentGif = $currentGifs[$filename] ?? null;

            if (null === $currentGif) {
                $warnings[] = sprintf('Warning: filename %s does not exist. Skipping...', $filename);

                continue;
            }

            $quote = \str_replace('"', '', $gif['Quote']);
            $quote = empty($quote) ? $gif['Caption'] : $quote;

            $slug = $this->slugger->slug($quote)->lower()->__toString();

            if ($currentGif['slug'] !== $slug) {
                $redirections[] = [
                    'old' => $currentGif['slug'],
                    'new' => $slug,
                ];
            }

            $characters = [
                $gif['Character 1'],
                $gif['Character 2'],
                $gif['Character 3'],
                $gif['Character 4'],
                $gif['Character 5'],
            ];

            $charactersSpeaking = [
                $gif['Speaker 1'],
                $gif['Speaker 2'],
                $gif['Speaker 3'],
            ];

            $episode = $gif['Episode'];

            $gifs[] = [
                'quote' => \stripslashes($quote),
                'characters' => array_filter($characters, fn ($value) => !empty($value)),
                'characters_speaking' => array_filter($charactersSpeaking, fn ($value) => !empty($value)),
                'filename' => $filename,
                'slug' => $slug,
                'episode' => 'Film' === $episode ? null : $episode,
            ];
        }

        foreach ($gifs as $gif) {
            $currentGifs[$gif['filename']] = $gif;
        }

        if (count($warnings)) {
            $output->writeln($warnings);
            $output->writeln('');
        }

        file_put_contents($this->gifsJsonFile, json_encode(array_values($currentGifs), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));

        $output->writeln(sprintf('%d/%d GIFs updated', count($gifs) - count($warnings), count($gifs)));

        if ([] !== $redirections) {
            /** @var array $currentRedirections */
            $currentRedirections = json_decode((string) file_get_contents($this->redirectionJsonFile), true);

            $allRedirections = array_merge($currentRedirections, $redirections);

            file_put_contents($this->redirectionJsonFile, json_encode($allRedirections, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
        }

        return Command::SUCCESS;
    }

    private function getCurrentGifs(): array
    {
        /** @var array $rawGifs */
        $rawGifs = json_decode((string) file_get_contents($this->gifsJsonFile), true);

        $gifs = [];

        /** @var array $gif */
        foreach ($rawGifs as $gif) {
            $gifs[(string) $gif['filename']] = $gif;
        }

        return $gifs;
    }
}
