<?php

namespace Omaressaouaf\TextMorph\Tests\Morphers;

use InvalidArgumentException;
use Omaressaouaf\TextMorph\Morphers\CompositeTextMorpher;
use Omaressaouaf\TextMorph\Morphers\RotationalTextMorpher;
use Omaressaouaf\TextMorph\Morphers\SubstitutionTextMorpher;
use PHPUnit\Framework\TestCase;

class CompositeTextMorpherTest extends TestCase
{
    public function test_it_rejects_morph_when_no_morphers_are_configured(): void
    {
        $morpher = new CompositeTextMorpher();

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('No morphers added to the composite morpher');

        $morpher->morph('hello');
    }

    public function test_it_rejects_unmorph_when_no_morphers_are_configured(): void
    {
        $morpher = new CompositeTextMorpher();

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('No morphers added to the composite morpher');

        $morpher->unmorph('hello');
    }

    public function test_it_morphs_using_registration_order(): void
    {
        $composite = new CompositeTextMorpher();
        $composite->add(new SubstitutionTextMorpher(['ab']));
        $composite->add(new RotationalTextMorpher(1));

        $substitution = new SubstitutionTextMorpher(['ab']);
        $rotational = new RotationalTextMorpher(1);

        $this->assertSame(
            $rotational->morph($substitution->morph('aabb')),
            $composite->morph('aabb')
        );
    }

    public function test_it_unmorphs_using_reverse_order(): void
    {
        $composite = new CompositeTextMorpher();
        $composite->add(new SubstitutionTextMorpher(['ab']));
        $composite->add(new RotationalTextMorpher(1));

        $substitution = new SubstitutionTextMorpher(['ab']);
        $rotational = new RotationalTextMorpher(1);
        $morphed = $rotational->morph($substitution->morph('aabb'));

        $this->assertSame(
            $substitution->unmorph($rotational->unmorph($morphed)),
            $composite->unmorph($morphed)
        );
    }

    public function test_unmorph_reverses_morph(): void
    {
        $composite = new CompositeTextMorpher();
        $composite->add(new SubstitutionTextMorpher(['ab', 'cd']));
        $composite->add(new RotationalTextMorpher(3));

        $original = 'Meet adam at 9pm!';

        $this->assertSame($original, $composite->unmorph($composite->morph($original)));
    }
}
