<?php
/**
 * Copyright © Magefan (support@magefan.com). All rights reserved.
 * Please visit Magefan.com for license details (https://magefan.com/end-user-license-agreement).
 */

declare(strict_types=1);

namespace Magefan\WebP\Model\Parser;

use Magefan\WebP\Api\AmpHtmlParserInterface;
use Magefan\WebP\Api\CreateWebPImageInterface;
use Magefan\WebP\Api\GetWebPUrlInterface;
use Magefan\WebP\Api\GetOriginWebPUrlInterface;

class AmpHtml implements AmpHtmlParserInterface
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
     * @var GetOriginWebPUrlInterface
     */
    private $getOriginWebPUrl;

    /**
     * Amp Html Parser constructor.
     * @param CreateWebPImageInterface $createWebPImage
     * @param GetWebPUrlInterface $getWebPUrl
     * @param GetOriginWebPUrlInterface $getOriginWebPUrl
     */
    public function __construct(
        CreateWebPImageInterface $createWebPImage,
        GetWebPUrlInterface $getWebPUrl,
        GetOriginWebPUrlInterface $getOriginWebPUrl
    ) {
        $this->createWebPImage = $createWebPImage;
        $this->getWebPUrl = $getWebPUrl;
        $this->getOriginWebPUrl = $getOriginWebPUrl;
    }

    /**
     * @param string $output
     * @return string
     */
    public function execute(string  $output): string
    {
        $origOutput = $output;
        $output = $this->prepareAmpImgTag($output);

        $matches = [];
        //$regex = '/<([^<]+)\ (src|data-src)=[\"\']([^\"\']+)\.(png|jpg|jpeg|PNG|JPG|JPEG|svg|webp|gif|GIF)([^>]+)>\s+<\/amp-img>/mi';
        //$regex = '/<([^<]+)\s+(src|data-src)=[\"\']([^\"\']+)\.(png|jpg|jpeg|PNG|JPG|JPEG|svg|webp|gif|GIF)([^>]+)>|>\s+<\/amp-img>/mi';
        $regex = "/<amp-img\s[^>]*?src\s*=\s*['\"]([^'\"]*?)['\"][^>]*?>/mi";

        if (preg_match_all($regex, $output, $matches, PREG_OFFSET_CAPTURE) === false) {
            return $origOutput;
        }

        $position = 0;

        foreach ($matches[0] as $i => $match) {
            $offset = $match[1] + $position;
            $htmlTag = $matches[0][$i][0];

            //$imageUrl = $matches[3][$i][0] . '.' . $matches[4][$i][0];
            $imageUrl = $matches[1][$i][0];

            $newHtmlTag = $this->getNewHtmlTag($imageUrl, $htmlTag);
            if (!$newHtmlTag) {
                continue;
            }

            $output = substr_replace($output, $newHtmlTag, $offset, strlen($htmlTag));
            $position = $position + (strlen($newHtmlTag) - strlen($htmlTag));
        }

        return $output;
    }

    /**
     * Return amp-image Tag (Html) with original and webp images
     * @param string $imageUrl
     * @param string $htmlTag
     * @return string
     */
    public function getNewHtmlTag(string $imageUrl, string $htmlTag): string
    {

        if (false !== strpos($htmlTag, 'data-webpconverted')) {
            return '';
        }

        /* Disable WebP for jQuery Plugin for Revolution Background Slider */
        if (false !== strpos($htmlTag, 'rev-slidebg')) {
            return '';
        }

        $webpUrl = '';
        $webPOriginImageUrl = $imageUrl;

        $extension = explode('.', $imageUrl);
        $extension = strtolower($extension[count($extension) - 1]);

        if ($extension) {
            if ($extension == 'svg') {
                return '';
            }

            if ($extension == 'webp') {
                /* Fix for media galery jpg & webp load when webp is not supported */
                $webPOriginImageUrl = $this->getOriginWebPUrl->execute($imageUrl);
                if ($webPOriginImageUrl) {
                    $webPOriginImageUrl = $webPOriginImageUrl;
                    $htmlTag = str_replace($imageUrl, $webPOriginImageUrl, $htmlTag);
                    $webpUrl = $imageUrl;
                } else {
                    return '';
                }
            }
        }

        if (!$webpUrl) {
            $success = $this->createWebPImage->execute($imageUrl);
            if (!$success) {
                return '';
            }

            $webpUrl = $this->getWebPUrl->execute($imageUrl);
        }

        $htmlTagAttributes = preg_replace(
            '/\s+(?:src|data-src|data-lazyload|data-src-retina|data-original|type|srcset)\s*=\s*(?:"[^"]*"|\'[^\']*\')/i',
            '',
            $htmlTag
        );

        $htmlTagAttributes = ltrim($htmlTagAttributes, '<amp-img');
        $htmlTagAttributes = rtrim($htmlTagAttributes, '/>');
        $htmlTagAttributes = str_replace('>', '', $htmlTagAttributes);
        $htmlTagAttributes = str_replace('owl-lazy', '', $htmlTagAttributes);
        $htmlTag = str_replace('<amp-img', '<amp-img fallback ', $htmlTag);

        $htmlTagAttributes = str_replace('decoding="async"', '', $htmlTagAttributes);
        $htmlTag = str_replace(
            ['decoding="async"', 'data-hero'],
            ['', ''],
            $htmlTag
        );

        $html =
            '<amp-img  src="'.$webpUrl.'" ' . $htmlTagAttributes . ' data-webpconverted="1">'
            . $htmlTag .
            '</amp-img>';

        $html = str_replace(
            ['data-width-amp', 'data-height-amp'],
            ['width', 'height'],
            $html
        );

        return $html;
    }

    /**
     * Convert <img> to <amp-img> tag
     * @param string $html
     * @return string
     */
    private function prepareAmpImgTag($html)
    {
        /* Replace width value with data-width-amp value */
        $html = preg_replace('#(<img\s+[^>]*)(?:width=(?:"\w+"|\'\w+\'))([^>]*)(?:data-width-amp="(\w+)")([^>]*>)#isU', '$1 width="$3" $2 $4', $html);
        $html = preg_replace('#(<img\s+[^>]*)(?:height=(?:"\w+"|\'\w+\'))([^>]*)(?:data-height-amp="(\w+)")([^>]*>)#isU', '$1 height="$3" $2 $4', $html);

        /* replace data-width-amp with width */
        $html = preg_replace('#(<img\s+[^>]*)(?:data-width-amp="(\w+)")([^>]*\/?>)#isU', '$1 width="$2" $3', $html);
        $html = preg_replace('#(<img\s+[^>]*)(?:data-height-amp="(\w+)")([^>]*\/?>)#isU', '$1 height="$2" $3', $html);

        /* Add height & width if not exists */
        $html = preg_replace('#(?:<img\s+)((?:(?!height=(?:"\w+"|\'\w+\')).)*)(?:\/>|>)#isU', '<img height="100" $1/>', $html);
        $html = preg_replace('#(?:<img\s+)((?:(?!width=(?:"\w+"|\'\w+\')).)*)(?:\/>|>)#isU', '<img width="290" $1/>', $html);

        $html = preg_replace('#<img\s+([^>]*)(?:data-src="([^"]*)")([^>]*)\/?>#isU', '<img src="$2" $1 $3/>', $html);

        /* Replace img to amp-img */
        $html = preg_replace('#(?:<img\s+)(.*?)(?:\/>|>)#is', '<amp-img $1></amp-img>', $html);

        return $html;
    }
}
