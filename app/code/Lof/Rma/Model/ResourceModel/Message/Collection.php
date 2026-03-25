<?php
/**
 * LandOfCoder
 * 
 * NOTICE OF LICENSE
 * 
 * This source file is subject to the Venustheme.com license that is
 * available through the world-wide-web at this URL:
 * http://www.venustheme.com/license-agreement.html
 * 
 * DISCLAIMER
 * 
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 * 
 * @category   LandOfCoder
 * @package    Lof_Rma
 * @copyright  Copyright (c) 2016 Venustheme (http://www.LandOfCoder.com/)
 * @license    http://www.LandOfCoder.com/LICENSE-1.0.html
 */



namespace Lof\Rma\Model\ResourceModel\Message;

/**
 * @method \Lof\Rma\Model\Message getFirstItem()
 * @method \Lof\Rma\Model\Message getLastItem()
 * @method \Lof\Rma\Model\ResourceModel\Message\Collection|\Lof\Rma\Model\Message[] addFieldToFilter
 * @method \Lof\Rma\Model\ResourceModel\Message\Collection|\Lof\Rma\Model\Message[] setOrder
 */
class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    public function __construct(
        \Magento\Framework\Data\Collection\EntityFactoryInterface $entityFactory,
        \Psr\Log\LoggerInterface $logger,
        \Magento\Framework\Data\Collection\Db\FetchStrategyInterface $fetchStrategy,
        \Magento\Framework\Event\ManagerInterface $eventManager,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        $connection = null,
        \Magento\Framework\Model\ResourceModel\Db\AbstractDb $resource = null
    ) {
        $this->entityFactory = $entityFactory;
        $this->logger        = $logger;
        $this->fetchStrategy = $fetchStrategy;
        $this->eventManager  = $eventManager;
        $this->storeManager  = $storeManager;
        $this->connection    = $connection;
        $this->resource      = $resource;

        parent::__construct($entityFactory, $logger, $fetchStrategy, $eventManager, $connection, $resource);
    }

    /**
     * {@inheritdoc}
     */
    protected function _construct()
    {
        $this->_init('Lof\Rma\Model\Message', 'Lof\Rma\Model\ResourceModel\Message');
    }

    /**
     * {@inheritdoc}
     */
    public function toOptionArray($emptyOption = false)
    {
        $arr = [];
        if ($emptyOption) {
            $arr[0] = ['value' => 0, 'label' => __('-- Please Select --')];
        }
        /** @var \Lof\Rma\Model\Message $item */
        foreach ($this as $item) {
            $arr[] = ['value' => $item->getId(), 'label' => $item->getName()];
        }

        return $arr;
    }

    /**
     * @param string|false $emptyOption
     *
     * @return array
     */
    public function getOptionArray($emptyOption = false)
    {
        $arr = [];
        if ($emptyOption) {
            $arr[0] = __('-- Please Select --');
        }
        /** @var \Lof\Rma\Model\Message $item */
        foreach ($this as $item) {
            $arr[$item->getId()] = $item->getName();
        }

        return $arr;
    }

    /**
     *
     */
    protected function initFields()
    {
        $select = $this->getSelect();
       
        $select->joinLeft(
            ['user' => $this->getTable('admin_user')],
            'main_table.user_id = user.user_id',
            ['user_name' => 'CONCAT(user.firstname, " ", user.lastname)']
        );
        $this->setOrder('created_at');
    }

    /**
     * {@inheritdoc}
     */
    protected function _initSelect()
    {
        parent::_initSelect();
        $this->initFields();
    }

     /************************/
}
