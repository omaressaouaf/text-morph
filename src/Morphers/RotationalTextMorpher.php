<?php

namespace Omaressaouaf\TextMorph\Morphers;

use Omaressaouaf\TextMorph\Contracts\TextMorpher;

/**
 * Rotates each letter in ALPHABET by a fixed shift, wrapping at both ends.
 * Non-alphabet characters are left unchanged.
 */
class RotationalTextMorpher implements TextMorpher
{
    public const ALPHABET = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';

    private int $shift;

    public function __construct(int $shift)
    {
        $this->validateShift($shift);

        $this->shift = $shift;
    }

    private function validateShift(int $shift): void
    {
        if ($shift < 0 || $shift > strlen(self::ALPHABET)) {
            throw new \InvalidArgumentException('Shift must be between 0 and ' . strlen(self::ALPHABET));
        }
    }

    /**
     * Encodes text by rotating each alphabet character forward by the configured shift.
     * Examples:
     *      shift = 1, input = "a", output = "b"
     *      shift = 2, input = "z", output = "B"
     *      shift = 1, input = "Z", output = "a"
     */
    public function morph(string $text): string
    {
        return $this->transform($text, 1);
    }

    /**
     * Decodes text by rotating each alphabet character backward by the configured shift.
     * Examples:
     *      shift = 1, input = "b", output = "a"
     *      shift = 2, input = "B", output = "z"
     *      shift = 1, input = "a", output = "Z"
     */
    public function unmorph(string $text): string
    {
        return $this->transform($text, -1);
    }

    private function transform(string $text, int $direction): string
    {
        $alphabetLength = strlen(self::ALPHABET);
        $result = '';

        for ($i = 0, $textLength = strlen($text); $i < $textLength; $i++) {
            $char = $text[$i];
            $position = strpos(self::ALPHABET, $char);

            if ($position === false) {
                $result .= $char;
                continue;
            }

            $newPosition = ($position + ($direction * $this->shift) + $alphabetLength) % $alphabetLength;
            $result .= self::ALPHABET[$newPosition];
        }

        return $result;
    }
}
