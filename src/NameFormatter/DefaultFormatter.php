<?php

namespace App\NameFormatter;

class DefaultFormatter implements Formatter
{
    public function format(string $string): string
    {
        $formattedName = preg_replace("/[\W_-]+/", "-", $string);

        if ($formattedName[strlen($formattedName) - 1] == '-') {
            $formattedName = substr($formattedName, 0, -1);
        }

        return $formattedName;
    }
}
