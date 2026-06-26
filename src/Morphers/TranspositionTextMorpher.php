<?php

namespace Omaressaouaf\TextMorph\Morphers;

use Omaressaouaf\TextMorph\Contracts\TextMorpher;

/**
 * Reorders characters using a rail-fence (zigzag) transposition cipher.
 * morph() writes text in a zigzag across the configured rails, then reads each rail left-to-right.
 */
class TranspositionTextMorpher implements TextMorpher
{
    private int $rails;

    public function __construct(int $rails)
    {
        $this->validateRails($rails);

        $this->rails = $rails;
    }

    private function validateRails(int $rails): void
    {
        if ($rails < 2) {
            throw new \InvalidArgumentException('Rails must be at least 2');
        }
    }

    /**
     * Reorders text by writing it in a zigzag across the rails.
     * Example:
     *      rails = 3, input = "HELLO", output = "HOELL"
     */
    public function morph(string $text): string
    {
        if ($text === '') {
            return '';
        }

        $sequence = $this->buildRailSequence(strlen($text));
        $rows = array_fill(0, $this->rails, '');

        for ($i = 0, $textLength = strlen($text); $i < $textLength; $i++) {
            $rows[$sequence[$i]] .= $text[$i];
        }

        return implode('', $rows);
    }

    /**
     * Restores text by splitting the morphed string back into rails and reading the zigzag.
     */
    public function unmorph(string $text): string
    {
        if ($text === '') {
            return '';
        }

        $textLength = strlen($text);
        $sequence = $this->buildRailSequence($textLength);
        $counts = array_fill(0, $this->rails, 0);

        foreach ($sequence as $rail) {
            $counts[$rail]++;
        }

        $rows = [];
        $offset = 0;

        for ($rail = 0; $rail < $this->rails; $rail++) {
            $rows[$rail] = substr($text, $offset, $counts[$rail]);
            $offset += $counts[$rail];
        }

        $pointers = array_fill(0, $this->rails, 0);
        $result = '';

        for ($i = 0; $i < $textLength; $i++) {
            $rail = $sequence[$i];
            $result .= $rows[$rail][$pointers[$rail]];
            $pointers[$rail]++;
        }

        return $result;
    }

    /**
     * @return list<int>
     */
    private function buildRailSequence(int $length): array
    {
        $sequence = [];
        $rail = 0;
        $direction = 1;

        for ($i = 0; $i < $length; $i++) {
            $sequence[] = $rail;

            if ($rail === $this->rails - 1) {
                $direction = -1;
            } elseif ($rail === 0) {
                $direction = 1;
            }

            $rail += $direction;
        }

        return $sequence;
    }
}
