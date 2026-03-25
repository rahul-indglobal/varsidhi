<?php
/**
 * Copyright © 2018 Ideo. All rights reserved.
 * @license GPLv3
 */

namespace Ideo\StoreLocator\Controller\Adminhtml\Stores;

use \Ideo\StoreLocator\Controller\Adminhtml\Stores;

class Index extends Stores
{
    /**
     * {@inheritdoc}
     */
    public function execute()
    {
        $resultPage = $this->_initAction();
        $resultPage->getConfig()->getTitle()->prepend(__('Store Locator - Stores'));

        return $resultPage;
    }
}
