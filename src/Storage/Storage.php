<?php

namespace App\Storage;

use App\Client\ClientException;
use App\Client\ClientInterface;
use mysql_xdevapi\Exception;

final class Storage
{
    public function __construct(private readonly ClientInterface $client)
    {
    }

    public function save(string $directory, string $fileName, string $content): void
    {
        $this->makeDir($directory);
        $result = $this->saveFile($directory . DIRECTORY_SEPARATOR . $fileName, $content);

        if (!$result) {
            throw new StorageException("Cant save page");
        }
    }

    public function saveFile(string $path, string $content) : false|int
    {
        return @file_put_contents($path, $content);
    }

    public function download(string $url, string $directory, string $fileName) :void
    {
        $this->makeDir($directory);

        try {
            $this->client->save($url, $directory . DIRECTORY_SEPARATOR . $fileName);
        } catch (ClientException $e) {
            throw new StorageException("Cant download " . $url . " in " . $directory . DIRECTORY_SEPARATOR . $fileName);
        }
    }

    public function get(string $path) :string
    {
        $data = file_get_contents($path);
        if ($data === false) {
            throw new StorageException('cant get data');
        }

        return $data;
    }

    private function makeDir(string $directory): void
    {
        if (!@is_dir($directory)) {
            $result = @mkdir($directory, 0755, true);
            if ($result === false) {
                throw new StorageException("Cant create directory " . $directory);
            }
        }
    }
}
