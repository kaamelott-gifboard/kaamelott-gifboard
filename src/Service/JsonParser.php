<?php

declare(strict_types=1);

namespace App\Service;

class JsonParser
{
    private string $gifsJsonFile;

    public function __construct(string $gifsJsonFile)
    {
        $this->gifsJsonFile = $gifsJsonFile;
    }

    public function findAll(): array
    {
        return $this->formatResult($this->getGifs());
    }

    public function findByQuote(string $search): array
    {
        $results = [];

        foreach ($this->getGifs() as $gif) {
            if ($this->match($search, $gif->quote)) {
                $results[] = $gif;
            }
        }

        return $this->formatResult($results);
    }

    public function findByCharacter(string $search): array
    {
        $results = [];

        foreach ($this->getGifs() as $gif) {
            foreach ($gif->characters as $character) {
                if ($this->match($search, $character)) {
                    $results[$gif->filename] = $gif;
                }
            }
        }

        return $this->formatResult($results);
    }

    public function findCharacters(): array
    {
        $results = [];

        foreach ($this->getGifs() as $gif) {
            foreach ($gif->characters as $character) {
                if (!\in_array($character, $results)) {
                    $results[] = $character;
                }
            }
        }

        sort($results);

        return ['characters' => $results];
    }

    private function getGifs(): array
    {
        $json = file_get_contents($this->gifsJsonFile);

        return json_decode($json);
    }

    private function match(string $search, string $subject): bool
    {
        return (bool) preg_match(sprintf('#%s#i', $search), $subject);
    }

    private function formatResult(array $results): array
    {
        return ['gifs' => $results];
    }
}
