<?php
/**
 * Copyright © 2018 Ideo. All rights reserved.
 * @license GPLv3
 */

namespace Ideo\StoreLocator\Model\ResourceModel\Store;

use \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use \Ideo\StoreLocator\Model\Store as Model;
use \Ideo\StoreLocator\Model\ResourceModel\Store as ResourceModel;

class Collection extends AbstractCollection
{
    /**
     * @var string
     */
    protected $_idFieldName = 'store_id';

    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(Model::class, ResourceModel::class);
    }
}
