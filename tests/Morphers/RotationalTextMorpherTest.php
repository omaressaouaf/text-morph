<?php

namespace Omaressaouaf\TextMorph\Tests\Morphers;

use InvalidArgumentException;
use Omaressaouaf\TextMorph\Morphers\RotationalTextMorpher;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class RotationalTextMorpherTest extends TestCase
{
    #[DataProvider('invalidShiftProvider')]
    public function test_it_rejects_invalid_shifts(int $shift): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessageIs('Shift must be between 0 and 52');

        new RotationalTextMorpher($shift);
    }

    public static function invalidShiftProvider(): array
    {
        return [
            'negative shift' => [-1],
            'shift above max' => [53],
        ];
    }

    #[DataProvider('morphProvider')]
    public function test_it_morphs_text(int $shift, string $input, string $expected): void
    {
        $morpher = new RotationalTextMorpher($shift);

        $this->assertSame($expected, $morpher->morph($input));
    }

    public static function morphProvider(): array
    {
        return [
            'shifts lowercase forward' => [1, 'a', 'b'],
            'wraps lowercase to uppercase' => [2, 'z', 'B'],
            'wraps uppercase to lowercase' => [1, 'Z', 'a'],
            'preserves non-alphabetic characters' => [1, 'Hello, World! 123', 'Ifmmp, Xpsme! 123'],
            'handles empty string' => [13, '', ''],
            'shift zero leaves text unchanged' => [0, 'abcXYZ', 'abcXYZ'],
            'full cycle shift leaves text unchanged' => [52, 'abcXYZ', 'abcXYZ'],
        ];
    }

    #[DataProvider('unmorphProvider')]
    public function test_it_unmorphs_text(int $shift, string $input, string $expected): void
    {
        $morpher = new RotationalTextMorpher($shift);

        $this->assertSame($expected, $morpher->unmorph($input));
    }

    public static function unmorphProvider(): array
    {
        return [
            'shifts lowercase backward' => [1, 'b', 'a'],
            'wraps uppercase to lowercase' => [2, 'B', 'z'],
            'wraps lowercase to uppercase' => [1, 'a', 'Z'],
            'preserves non-alphabetic characters' => [1, 'Ifmmp, Xpsme! 123', 'Hello, World! 123'],
            'handles empty string' => [13, '', ''],
            'shift zero leaves text unchanged' => [0, 'abcXYZ', 'abcXYZ'],
            'full cycle shift leaves text unchanged' => [52, 'abcXYZ', 'abcXYZ'],
        ];
    }

    #[DataProvider('roundTripShiftProvider')]
    public function test_unmorph_reverses_morph(int $shift): void
    {
        $morpher = new RotationalTextMorpher($shift);
        $original = 'The quick brown fox jumps over 13 lazy dogs!';

        $this->assertSame($original, $morpher->unmorph($morpher->morph($original)));
    }

    public static function roundTripShiftProvider(): array
    {
        return [
            'shift 1' => [1],
            'shift 2' => [2],
            'shift 13' => [13],
            'half alphabet' => [26],
            'full cycle' => [52],
        ];
    }
}
