<?php

namespace Omaressaouaf\TextMorph\Morphers;

use Omaressaouaf\TextMorph\Contracts\TextMorpher;

/**
 * Swaps character pairs bidirectionally. Each pair defines a two-way substitution
 * that preserves letter case. Non-mapped characters are left unchanged.
 */
class SubstitutionTextMorpher implements TextMorpher
{
    /** @var array<string, string> */
    private array $substitutionMap;

    /**
     * @param list<string> $pairs
     */
    public function __construct(array $pairs)
    {
        $this->validatePairs($pairs);

        $this->substitutionMap = $this->buildSubstitutionMap($pairs);
    }

    /**
     * @param list<string> $pairs
     */
    private function validatePairs(array $pairs): void
    {
        $usedChars = [];

        foreach ($pairs as $pair) {
            if (strlen($pair) !== 2) {
                throw new \InvalidArgumentException('Each substitution pair must contain exactly 2 characters');
            }

            $char1 = $pair[0];
            $char2 = $pair[1];

            if ($char1 === $char2) {
                throw new \InvalidArgumentException('Characters in a substitution pair cannot be the same');
            }

            if (isset($usedChars[$char1]) || isset($usedChars[$char2])) {
                throw new \InvalidArgumentException('Each character can only appear in one substitution pair');
            }

            $usedChars[$char1] = true;
            $usedChars[$char2] = true;
        }
    }

    /**
     * @param list<string> $pairs
     *
     * @return array<string, string>
     */
    private function buildSubstitutionMap(array $pairs): array
    {
        $substitutionMap = [];

        foreach ($pairs as $pair) {
            $substitutionMap[strtolower($pair[0])] = strtolower($pair[1]);
            $substitutionMap[strtolower($pair[1])] = strtolower($pair[0]);
            $substitutionMap[strtoupper($pair[0])] = strtoupper($pair[1]);
            $substitutionMap[strtoupper($pair[1])] = strtoupper($pair[0]);
        }

        return $substitutionMap;
    }

    /**
     * Swaps characters using the configured pairs.
     * Each pair is bidirectional (e.g. "ab" swaps a↔b), so applying morph twice restores the original text.
     * Examples:
     *      pairs = ["ab"], input = "aabbcc", output = "bbaacc"
     *      pairs = ["ab", "cd"], input = "adam", output = "bcbm"
     */
    public function morph(string $text): string
    {
        return $this->transform($text);
    }

    /**
     * Reverses a prior morph by applying the same swap transform.
     * Identical to morph() because each pair defines a two-way swap — swapping a↔b undoes swapping a↔b.
     */
    public function unmorph(string $text): string
    {
        return $this->transform($text);
    }

    private function transform(string $text): string
    {
        $result = '';

        for ($i = 0, $textLength = strlen($text); $i < $textLength; $i++) {
            $char = $text[$i];
            $result .= $this->substitutionMap[$char] ?? $char;
        }

        return $result;
    }
}
