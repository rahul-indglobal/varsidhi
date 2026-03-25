<?php
/**
 * Copyright © Magefan (support@magefan.com). All rights reserved.
 * Please visit Magefan.com for license details (https://magefan.com/end-user-license-agreement).
 */

declare(strict_types = 1);

namespace Magefan\WebP\Plugin\Magento\Framework\View;

use Magento\Framework\View\LayoutInterface as LayoutInterfaceBase;
use Magefan\WebP\Model\Config;
use Magefan\WebP\Api\HtmlParserInterface as HtmlParser;
use Magento\Framework\App\RequestInterface;

/**
 * Class LayoutInterface
 */
class LayoutInterface
{
    /**
     * @var HtmlParser
     */
    private $htmlParser;

    /**
     * @var RequestInterface
     */
    private $request;

    /**
     * LayoutInterface constructor.
     * @param Config $config
     * @param HtmlParser $htmlParser
     * @param RequestInterface $request
     */
    public function __construct(
        Config $config,
        HtmlParser $htmlParser,
        RequestInterface $request = null
    ) {
        $this->config = $config;
        $this->htmlParser = $htmlParser;
        $this->request = $request ?: \Magento\Framework\App\ObjectManager::getInstance()
            ->get(\Magento\Framework\App\RequestInterface::class);
    }

    /**
     * @param LayoutInterfaceBase $layout
     * @param $output
     * @return mixed
     */
    public function afterGetOutput(LayoutInterfaceBase $layout, string  $output): string
    {

        if (!$this->config->isEnabled()) {
            return $output;
        }

        /**
         * MGS_Fbuilder compatibity (disable webp if frontend builder enabled )
        */
        if ($this->request->getModuleName() == 'mpanel' && ($this->request->getControllerName() == 'create' || $this->request->getControllerName() == 'edit')) {
            return $output;
        }

        $handles = $layout->getUpdate()->getHandles();
        $blackListHandles = [
            'sales_email_order_invoice_items'
        ];

        if (empty($handles) || array_intersect($blackListHandles, $handles)) {
            return $output;
        }

        return $this->htmlParser->execute($output);
    }
}
