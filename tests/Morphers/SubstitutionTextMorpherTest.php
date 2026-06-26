<?php

namespace Omaressaouaf\TextMorph\Tests\Morphers;

use InvalidArgumentException;
use Omaressaouaf\TextMorph\Morphers\SubstitutionTextMorpher;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class SubstitutionTextMorpherTest extends TestCase
{
    #[DataProvider('invalidPairProvider')]
    public function test_it_rejects_invalid_pairs(array $pairs, string $message): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage($message);

        new SubstitutionTextMorpher($pairs);
    }

    public static function invalidPairProvider(): array
    {
        return [
            'pair must contain two characters' => [
                ['a'],
                'Each substitution pair must contain exactly 2 characters',
            ],
            'characters in a pair cannot match' => [
                ['aa'],
                'Characters in a substitution pair cannot be the same',
            ],
            'character cannot appear in multiple pairs' => [
                ['ab', 'bc'],
                'Each character can only appear in one substitution pair',
            ],
        ];
    }

    #[DataProvider('morphProvider')]
    public function test_it_morphs_text(array $pairs, string $input, string $expected): void
    {
        $morpher = new SubstitutionTextMorpher($pairs);

        $this->assertSame($expected, $morpher->morph($input));
    }

    public static function morphProvider(): array
    {
        return [
            'swaps a single pair' => [['ab'], 'aabbcc', 'bbaacc'],
            'swaps multiple pairs' => [['ab', 'cd'], 'adam', 'bcbm'],
            'preserves letter case' => [['ab'], 'AaBb', 'BbAa'],
            'preserves non-mapped characters' => [['ab'], 'a!b 1', 'b!a 1'],
            'handles empty string' => [['ab'], '', ''],
            'handles empty pairs' => [[], 'hello', 'hello'],
        ];
    }

    #[DataProvider('morphProvider')]
    public function test_unmorph_is_identical_to_morph(array $pairs, string $input, string $expected): void
    {
        $morpher = new SubstitutionTextMorpher($pairs);

        $this->assertSame($expected, $morpher->unmorph($input));
        $this->assertSame($input, $morpher->unmorph($morpher->morph($input)));
    }
}
