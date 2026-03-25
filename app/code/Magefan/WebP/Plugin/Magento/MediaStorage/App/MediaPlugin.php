<?php
/**
 * Copyright © Magefan (support@magefan.com). All rights reserved.
 * Please visit Magefan.com for license details (https://magefan.com/end-user-license-agreement).
 */

namespace Magefan\WebP\Plugin\Magento\MediaStorage\App;

use Exception;
use LogicException;
use Magento\MediaStorage\App\Media;
use Magento\Framework\HTTP\PhpEnvironment\Request;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Stdlib\Cookie\PhpCookieReader;
use Magento\MediaStorage\Model\File\Storage\Config;

/**
 * Class MediaPlugin
 */
class MediaPlugin
{
    /**
     * @var Config
     */
    private $config;

    /**
     * @var
     */
    private $directoryMedia;

    /**
     * @var mixed
     */
    private $response;

    /**
     * @var mixed
     */
    private $placeholderFactory;

    /**
     * @var string
     */
    private $mediaDirectoryPath;

    /**
     * @param Media $subject
     * @param callable $proceed
     * @return mixed
     */
    public function aroundLaunch(Media $subject, callable $proceed)
    {
        $request = new \Magento\MediaStorage\Model\File\Storage\Request(
            new Request(
                new PhpCookieReader(),
                new \Magento\Framework\Stdlib\StringUtils()
            )
        );
        $relativeFileName = $request->getPathInfo();

        if (false !== strpos($relativeFileName, 'mf_webp') && false !== strpos($relativeFileName, '.webp')) {

            /* Construct properties
             * we do this via object manager :-(
             * it's necessary to do in this area
             */
            $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
            $this->config = $objectManager->get(\Magefan\WebP\Model\Config::class);

            if ($this->config->isEnabled()) {

                $appState = $objectManager->get(\Magento\Framework\App\State::class);
                $configFactory = $objectManager->get(\Magento\MediaStorage\Model\File\Storage\ConfigFactory::class);
                $filesystem = $objectManager->get(\Magento\Framework\Filesystem::class);
                $directoryPub = $filesystem->getDirectoryWrite(DirectoryList::PUB);
                $this->directoryMedia = $filesystem->getDirectoryWrite(DirectoryList::MEDIA);
                $syncFactory = $objectManager->get(\Magento\MediaStorage\Model\File\Storage\SynchronizationFactory::class);
                $getOriginWebUrl = $objectManager->get(\Magefan\WebP\Model\GetOriginWebPUrl::class);
                $createWebPImage = $objectManager->get(\Magefan\WebP\Model\CreateWebPImage::class);
                $this->response = $objectManager->get(\Magento\MediaStorage\Model\File\Storage\Response::class);
                $this->placeholderFactory = $objectManager->get(\Magento\Catalog\Model\View\Asset\PlaceholderFactory::class);

                $configCacheFile = BP . '/var/resource_config.json';

                $appState->setAreaCode(\Magento\Framework\App\Area::AREA_GLOBAL);
                if ($this->checkMediaDirectoryChanged()) {
                    // Path to media directory changed or absent - update the config
                    /** @var Config $config */
                    $config = $configFactory->create(['cacheFile' => $configCacheFile]);
                    $config->save();
                    $this->mediaDirectoryPath = $config->getMediaDirectory();
                    $allowedResources = $config->getAllowedResources();
                    $isAllowed = function ($resource, array $allowedResources) {
                        foreach ($allowedResources as $allowedResource) {
                            if (0 === stripos($resource, $allowedResource)) {
                                return true;
                            }
                        }
                        return false;
                    };
                    if (!$isAllowed($relativeFileName, $allowedResources)) {
                        throw new LogicException('The path is not allowed: ' . $relativeFileName);
                    }
                }
                $relativeOriginPath = $getOriginWebUrl->execute($relativeFileName);

                if (strpos($relativeFileName, 'static/frontend/')) {
                    $relativeOriginPath = str_replace('media/', 'static/', $relativeOriginPath);
                }

                $generatedWebPImage = false;
                if ($relativeOriginPath) {
                    $generatedWebPImage = $createWebPImage->execute($relativeOriginPath);
                }

                if ($generatedWebPImage) {
                    try {
                        $sync = $syncFactory->create(['directory' => $directoryPub]);
                        $sync->synchronize($relativeFileName);
                        if ($directoryPub->isReadable($relativeOriginPath)) {
                            $this->response->setFilePath($directoryPub->getAbsolutePath($relativeOriginPath));
                        } else {
                            $this->setPlaceholderImage();
                        }

                    } catch (Exception $e) {
                        $this->setPlaceholderImage();
                    }
                } else {
                    $this->setPlaceholderImage();
                }

                return $this->response;
            }
        }

        return $proceed();
    }

    /**
     * Check if media directory changed
     *
     * @return bool
     */
    private function checkMediaDirectoryChanged(): bool
    {
        $mediaDirectoryPath = $this->mediaDirectoryPath ? rtrim($this->mediaDirectoryPath, '/') : '';
        $directoryMediaAbsolutePath = $this->directoryMedia->getAbsolutePath();
        $directoryMediaAbsolutePath = $directoryMediaAbsolutePath ? rtrim($directoryMediaAbsolutePath, '/') : '';
        return $mediaDirectoryPath !== $directoryMediaAbsolutePath;
    }

    /**
     * Set placeholder image into response
     *
     * @return void
     */
    private function setPlaceholderImage(): void
    {
        $placeholder = $this->placeholderFactory->create(['type' => 'image']);
        $this->response->setFilePath($placeholder->getPath());
    }
}
