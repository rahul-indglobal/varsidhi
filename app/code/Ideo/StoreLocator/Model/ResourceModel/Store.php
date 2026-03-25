<?php
/**
 * Copyright © 2018 Ideo. All rights reserved.
 * @license GPLv3
 */

namespace Ideo\StoreLocator\Model\ResourceModel;

use \Magento\Framework\Model\ResourceModel\Db\AbstractDb;
use \Magento\Framework\Model\ResourceModel\Db\Context;
use \Magento\Framework\Stdlib\DateTime\DateTime;
use \Magento\Framework\Model\AbstractModel;

class Store extends AbstractDb
{
    /**
     * @var \Magento\Framework\Stdlib\DateTime\DateTime
     */
    private $date;

    /**
     * Construct
     *
     * @param \Magento\Framework\Model\ResourceModel\Db\Context $context
     * @param \Magento\Framework\Stdlib\DateTime\DateTime $date
     * @param string|null $resourcePrefix
     */
    public function __construct(
        Context $context,
        DateTime $date,
        $resourcePrefix = null
    ) {
        $this->date = $date;
        parent::__construct($context, $resourcePrefix);
    }

    /**
     * {@inheritdoc}
     */
    protected function _construct()
    {
        $this->_init('ideo_storelocator_store', 'store_id');
    }

    /**
     * {@inheritdoc}
     */
    protected function _beforeSave(AbstractModel $object)
    {
        if ($object->isObjectNew() && !$object->hasCreationTime()) {
            $object->setCreationTime($this->date->gmtDate());
        }

        $object->setUpdateTime($this->date->gmtDate());

        return parent::_beforeSave($object);
    }
}
