<?php

namespace Omaressaouaf\TextMorph\Contracts;

interface TextMorpher
{
    public function morph(string $text): string;

    public function unmorph(string $text): string;
}
