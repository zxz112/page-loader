<?php

namespace App\Client;

use App\Url;

interface ClientInterface
{
    public function getContents(Url $url): string;
    public function save(string $url, string $to): void;
}
