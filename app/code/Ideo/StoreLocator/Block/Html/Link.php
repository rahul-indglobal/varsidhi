<?php
/**
 * Copyright © 2018 Ideo. All rights reserved.
 * @license GPLv3
 */

namespace Ideo\StoreLocator\Block\Html;

/**
 * Class Link
 *
 * @SuppressWarnings(PHPMD.DepthOfInheritance)
 */
class Link extends \Magento\Framework\View\Element\Html\Link
{
    /**
     * {@inheritdoc}
     */
    public function getHref()
    {
        return $this->_urlBuilder->getUrl('storelocator');
    }

    /**
     * @return bool
     */
    public function isCurrent()
    {
        return $this->getData('current') || $this->_urlBuilder->getCurrentUrl() === $this->getHref();
    }
}
