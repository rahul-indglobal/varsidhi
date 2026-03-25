<?php
/**
 * Copyright © 2018 Ideo. All rights reserved.
 * @license GPLv3
 */

namespace Ideo\StoreLocator\Controller\Adminhtml\Categories;

use \Ideo\StoreLocator\Controller\Adminhtml\Categories;

class Index extends Categories
{
    /**
     * {@inheritdoc}
     */
    public function execute()
    {
        $resultPage = $this->_initAction();
        $resultPage->getConfig()->getTitle()->prepend(__('Store Locator - Categories'));

        return $resultPage;
    }
}
