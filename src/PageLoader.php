<?php

namespace App;

use App\Client\ClientInterface;
use App\NameFormatter\DefaultFormatter;
use App\Storage\Storage;
use App\Storage\StorageException;
use Psr\Log\LoggerInterface;

final class PageLoader
{
    private NameManager $nameManager;
    private HtmlParser $htmlParser;
    private Url $url;
    private Storage $storage;
    public function __construct(
        string $url,
        private readonly ClientInterface $client,
        protected readonly LoggerInterface $logger
    ) {
        try {
            $this->url = new Url($url);
        } catch (\InvalidArgumentException $e) {
            $this->logger->error($e->getMessage());
            throw new \InvalidArgumentException($e->getMessage());
        }
        $this->nameManager = new NameManager(
            $this->url,
            new DefaultFormatter()
        );
        $this->htmlParser = new HtmlParser();
        $this->storage = new Storage($client);
    }
    public function loadPage(string $directory): string
    {
        $content = $this->client->getContents($this->url);
        $fileName = $this->nameManager->getPageName();
        $this->storage->save($directory, $fileName, $content);

        return $directory . DIRECTORY_SEPARATOR . $fileName;
    }

    public function processAssets(string $savedPagePath, string $directory): void
    {
        $this->htmlParser->load($savedPagePath, true);
        $assets = $this->htmlParser->getAssets();

        if (empty($assets)) {
            return;
        }

        $directoryFileName = $this->nameManager->getFilesDirectoryName();
        $assetsModifier = new AssetsModifier($this->url);
        $assetsForReplace = [];
        $assetForLoad = [];
        /**
         * @var string $originalAsset
         */
        foreach ($assets as $originalAsset) {
            if ($modifiedAsset = $assetsModifier->modify($originalAsset)) {
                $fileName = $this->nameManager->getAssetName($modifiedAsset);
                $assetsForReplace[$originalAsset] = $directoryFileName . DIRECTORY_SEPARATOR . $fileName;
                $assetForLoad[$originalAsset] = [
                    'url' => $this->url->getScheme() . "://" . $this->url->getHost() . $modifiedAsset,
                    'fileName' => $fileName
                ];
            }
        }

        foreach ($assetForLoad as $originalAsset => $assetData) {
            try {
                $this->storage->download(
                    $assetData['url'],
                    $directory . DIRECTORY_SEPARATOR . $directoryFileName,
                    $assetData['fileName']
                );
            } catch (StorageException $e) {
                $this->logger->warning($e->getMessage());
                unset($assetsForReplace[$originalAsset]);
            }
        }

        $html = $this->storage->get($savedPagePath);
        $modifiedHtml = $this->replaceOriginalAssets($html, $assetsForReplace);
        $this->storage->saveFile($savedPagePath, $modifiedHtml);
    }

    public function replaceOriginalAssets(string $html, array $assetsForReplace) :string
    {
        /**
         * @var array<string> $assetsForReplace
         */
        return str_replace(
            array_keys($assetsForReplace),
            array_values($assetsForReplace),
            $html
        );
    }

    public function load(string $directory) :string
    {
        try {
            $savedPagePath = $this->loadPage($directory);
            $this->processAssets($savedPagePath, $directory);
            return $savedPagePath;
        } catch (\Exception $e) {
            $this->logger->error($e->getMessage());
            throw new \Exception($e->getMessage());
        }
    }
}
