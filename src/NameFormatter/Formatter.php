<?php

namespace App\NameFormatter;

interface Formatter
{
    public function format(string $string): string;
}
