<?php

declare(strict_types=1);

namespace KaamelottGifboard\Command;

use KaamelottGifboard\Helper\ImageHelper;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Serializer\Encoder\DecoderInterface;
use Symfony\Component\String\Slugger\SluggerInterface;

class ImportCommand extends Command
{
    protected static $defaultName = 'import:gifs';

    public function __construct(
        private DecoderInterface $decoder,
        private string $gifsJsonFile,
        private ImageHelper $imageHelper,
        private SluggerInterface $slugger,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->setDescription('Import GIFs via CSV.')
            ->setHelp('This will add GIFs to the JSON file based on a CSV.')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        try {
            $file = \file_get_contents(__DIR__.'/gifs.csv');
        } catch (\Throwable) {
            $output->writeln('Error: Could not find the "gifs.csv". Aborting');

            return Command::FAILURE;
        }

        /** @var array $gifsFromCsv */
        $gifsFromCsv = $this->decoder->decode((string) $file, 'csv');

        $gifs = [];
        $warnings = [];

        foreach ($gifsFromCsv as $gif) {
            if ('Y' === $gif['Added']) {
                continue;
            }

            if (!$this->imageHelper->gifExists($gif['Filename'])) {
                $warnings[] = sprintf('Warning: %s.gif does not exist. Skipping...', $gif['Filename']);

                continue;
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

            $quote = \str_replace('"', '', $gif['Quote']);

            $gifs[] = [
                'quote' => !empty($quote) ? $quote : $gif['Caption'],
                'characters' => array_filter($characters, fn ($value) => !empty($value)),
                'characters_speaking' => array_filter($charactersSpeaking, fn ($value) => !empty($value)),
                'filename' => sprintf('%s.gif', $gif['Filename']),
                'slug' => '',
            ];
        }

        if (count($warnings)) {
            $output->writeln($warnings);
            $output->writeln('');
        }

        /** @var array $json */
        $json = json_decode((string) file_get_contents($this->gifsJsonFile), true);

        $newJson = array_merge($json, $gifs);

        $this->slugifyGifs($newJson);

        file_put_contents($this->gifsJsonFile, json_encode($newJson, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));

        $output->writeln(sprintf('%d/%d new GIFs imported', count($gifs) - count($warnings), count($gifs)));

        return Command::SUCCESS;
    }

    private function slugifyGifs(array &$newJson): void
    {
        foreach ($newJson as &$gif) {
            if ('' !== $gif['slug']) {
                continue;
            }

            $slug = $this->slugger->slug($gif['quote'])->lower()->__toString();

            $this->deduplicateSlug($newJson, $slug);

            $gif['slug'] = $slug;
        }
    }

    private function deduplicateSlug(array $gifs, string &$slug): void
    {
        $i = 0;

        do {
            $exist = array_filter($gifs, function (array $gif) use ($slug, $i) {
                if ($i > 0) {
                    return $gif['slug'] === sprintf('%s-%d', $slug, $i);
                }

                return $gif['slug'] === $slug;
            });

            if (!$exist && $i > 0) {
                $slug = sprintf('%s-%d', $slug, $i);
                break;
            }

            ++$i;
        } while ($exist);
    }
}
