<?php
/**
 * Copyright © Magefan (support@magefan.com). All rights reserved.
 * Please visit Magefan.com for license details (https://magefan.com/end-user-license-agreement).
 */

declare(strict_types = 1);

namespace Magefan\WebP\Model\Parser;

use Magefan\WebP\Api\CreateWebPImageInterface;
use Magefan\WebP\Api\GetWebPUrlInterface;
use Magefan\WebP\Model\Config;

class PageBuilder
{
    /**
     * @var CreateWebPImageInterface
     */
    private $createWebPImage;

    /**
     * @var GetWebPUrlInterface
     */
    private $getWebPUrl;

    /**
     * @var Config
     */
    private $config;

    const WEBP_SUPPOERTED = '.webp-supported';
    const NOT_WEBP = '.no-webp';

    /**
     * PageBuilder constructor.
     * @param CreateWebPImageInterface $createWebPImage
     * @param GetWebPUrlInterface $getWebPUrl
     * @param Config $config
     */
    public function __construct(
        CreateWebPImageInterface $createWebPImage,
        GetWebPUrlInterface $getWebPUrl,
        Config $config
    ) {
        $this->createWebPImage = $createWebPImage;
        $this->getWebPUrl = $getWebPUrl;
        $this->config = $config;
    }


    /**
     * @param string $result
     * @return string
     * @throws \Magento\Framework\Exception\FileSystemException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function processDataBackgroundImages(string $result):string
    {
        if (strpos($result, 'data-background-images') === false) {
            return $result;
        }

        $matches = [];
        $result = str_replace('data-background-images="{}"', "data-background-images='{}'", $result);
        $quote = (strpos($result, 'data-background-images="') !== false) ? '"' : "'";
        $regex = "/data-background-images=". $quote ."(.*?)". $quote ."/mi";

        if (preg_match_all($regex, $result, $matches, PREG_OFFSET_CAPTURE) === false) {
            return $result;
        }

        $position = 0;

        foreach ($matches[0] as $i => $match) {
            $originalAttributeValue = $matches[1][$i][0];
            if (strlen($originalAttributeValue) < 4) {
                continue;
            }

            $images = json_decode(stripslashes(html_entity_decode($originalAttributeValue)), true);

            if (empty($images)) {
                continue;
            }

            $offset = $matches[1][$i][1] + $position;

            foreach ($images as $imageDeviceType => $imageUrl) {
                if ($this->createWebPImage->execute($imageUrl)) {
                    $images[$imageDeviceType] = $this->getWebPUrl->execute($imageUrl);
                }
            }

            $newAttribute = htmlentities(json_encode($images));

            if (!$newAttribute) {
                continue;
            }

            $result = substr_replace($result, $newAttribute, $offset, strlen($originalAttributeValue));
            $position = $position + (strlen($newAttribute) - strlen($originalAttributeValue));
        }

        return $result;
    }

    /**
     * @param string $result
     * @return string
     * @throws \Magento\Framework\Exception\FileSystemException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function processBackgroundImages(string $result):string
    {
        $matches = [];
        $regex = "/(.background-image-\w+ ){background-image: url\((.*?)\);}/mi";
        if (preg_match_all($regex, $result, $matches, PREG_OFFSET_CAPTURE) === false) {
            return $result;
        }

        $position = 0;

        foreach ($matches[0] as $i => $match) {
            $originalStyles = $matches[0][$i][0];
            $class = $matches[1][$i][0];
            $imageUrl = $matches[2][$i][0];
            $offset = $match[1] + $position;

            $newStyles = $this->getNewStylesForOriginalImageAndWebP($originalStyles, $class, $imageUrl);
            if (!$newStyles) {
                continue;
            }

            $result = substr_replace($result, $newStyles, $offset, strlen($originalStyles));
            $position = $position + (strlen($newStyles) - strlen($originalStyles));
        }

        return $result;
    }

    /**
     * @param string $originalStyles
     * @param string $class
     * @param string $imageUrl
     * @return string
     * @throws \Magento\Framework\Exception\FileSystemException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    private function getNewStylesForOriginalImageAndWebP(string $originalStyles, string $class, string $imageUrl):string
    {
        if (!$this->createWebPImage->execute($imageUrl)) {
            return '';
        }

        $webpStyles = str_replace($class, self::WEBP_SUPPOERTED . ' ' . $class, $originalStyles);
        $webpStyles = str_replace($imageUrl, $this->getWebPUrl->execute($imageUrl), $webpStyles);

        return $webpStyles . ' ' . self::NOT_WEBP . ' ' . $originalStyles;
    }
}
