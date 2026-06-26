<?php

namespace Omaressaouaf\TextMorph\Morphers;

use Omaressaouaf\TextMorph\Contracts\TextMorpher;

/**
 * Chains multiple morphers together. morph() applies them in registration order;
 * unmorph() applies them in reverse order.
 */
class CompositeTextMorpher implements TextMorpher
{
    /** @var list<TextMorpher> */
    private array $morphers = [];

    public function add(TextMorpher $morpher): void
    {
        $this->morphers[] = $morpher;
    }

    /**
     * Applies each morpher in the order they were added.
     */
    public function morph(string $text): string
    {
        $this->assertMorphersAreConfigured();

        $result = $text;

        foreach ($this->morphers as $morpher) {
            $result = $morpher->morph($result);
        }

        return $result;
    }

    /**
     * Reverses a prior morph by applying each morpher's unmorph in reverse order.
     */
    public function unmorph(string $text): string
    {
        $this->assertMorphersAreConfigured();

        $result = $text;

        foreach (array_reverse($this->morphers) as $morpher) {
            $result = $morpher->unmorph($result);
        }

        return $result;
    }

    private function assertMorphersAreConfigured(): void
    {
        if ($this->morphers === []) {
            throw new \InvalidArgumentException('No morphers added to the composite morpher');
        }
    }
}
