<?php

namespace Omaressaouaf\TextMorph\Tests\Morphers;

use InvalidArgumentException;
use Omaressaouaf\TextMorph\Morphers\TranspositionTextMorpher;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class TranspositionTextMorpherTest extends TestCase
{
    #[DataProvider('invalidRailsProvider')]
    public function test_it_rejects_invalid_rails(int $rails): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Rails must be at least 2');

        new TranspositionTextMorpher($rails);
    }

    public static function invalidRailsProvider(): array
    {
        return [
            'zero rails' => [0],
            'one rail' => [1],
            'negative rails' => [-1],
        ];
    }

    #[DataProvider('morphProvider')]
    public function test_it_morphs_text(int $rails, string $input, string $expected): void
    {
        $morpher = new TranspositionTextMorpher($rails);

        $this->assertSame($expected, $morpher->morph($input));
    }

    public static function morphProvider(): array
    {
        return [
            'zigzags across three rails' => [3, 'HELLO', 'HOELL'],
            'zigzags across two rails' => [2, 'Secret', 'Sceert'],
            'preserves spaces and punctuation' => [3, 'Meet at 9pm!', 'M 9eta p!etm'],
            'handles empty string' => [3, '', ''],
            'handles single character' => [3, 'A', 'A'],
        ];
    }

    #[DataProvider('roundTripInputProvider')]
    public function test_unmorph_reverses_morph(int $rails, string $input): void
    {
        $morpher = new TranspositionTextMorpher($rails);

        $this->assertSame($input, $morpher->unmorph($morpher->morph($input)));
    }

    public static function roundTripInputProvider(): array
    {
        return [
            'three rails' => [3, 'HELLO'],
            'two rails' => [2, 'Secret'],
            'with punctuation' => [3, 'Meet at 9pm!'],
            'empty string' => [3, ''],
            'single character' => [3, 'A'],
        ];
    }

    #[DataProvider('roundTripRailsProvider')]
    public function test_unmorph_reverses_morph_for_longer_text(int $rails): void
    {
        $morpher = new TranspositionTextMorpher($rails);
        $original = 'The quick brown fox jumps over 13 lazy dogs!';

        $this->assertSame($original, $morpher->unmorph($morpher->morph($original)));
    }

    public static function roundTripRailsProvider(): array
    {
        return [
            'two rails' => [2],
            'three rails' => [3],
            'five rails' => [5],
        ];
    }
}
