<?php
/**
 * Webkul Software.
 *
 * @category  Webkul
 * @package   Webkul_StorePickup
 * @author    Webkul
 * @copyright Copyright (c) Webkul Software Private Limited (https://webkul.com)
 * @license   https://store.webkul.com/license.html
 */

namespace Webkul\StorePickup\Model\ResourceModel\Stores;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    /**
     * @var $_idFieldName
     */
    protected $_idFieldName = 'entity_id';

    /**
     * @var $_eventPrefix
     */
    protected $_eventPrefix = 'webkul_storepickup_stores_collection';

    /**
     * @var $_eventObject
     */
    protected $_eventObject = 'webkul_storepickup_stores_collection';

    /**
     * @var $collectionFactory
     */
    private $collectionFactory;

    /**
     * @var $priceHelper
     */
    private $priceHelper;

    /**
     * @var $dataHelper
     */
    private $dataHelper;

    /**
     * @param \Magento\Framework\Data\Collection\EntityFactoryInterface $entityFactory
     * @param \Psr\Log\LoggerInterface $logger
     * @param \Magento\Framework\Data\Collection\Db\FetchStrategyInterface $fetchStrategy
     * @param \Magento\Framework\Event\ManagerInterface $eventManager
     * @param \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $collectionFactory
     * @param \Magento\Framework\Pricing\Helper\Data $priceHelper
     * @param \Webkul\StorePickup\Helper\Data $dataHelper
     * @param \Magento\Framework\Event\ManagerInterface $eventManager
     * @param \Magento\Framework\DB\Adapter\AdapterInterface $connection
     * @param \Magento\Framework\Model\ResourceModel\Db\AbstractDb $resource
     */
    public function __construct(
        \Magento\Framework\Data\Collection\EntityFactoryInterface $entityFactory,
        \Psr\Log\LoggerInterface $logger,
        \Magento\Framework\Data\Collection\Db\FetchStrategyInterface $fetchStrategy,
        \Magento\Framework\Event\ManagerInterface $eventManager,
        \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $collectionFactory,
        \Magento\Framework\Pricing\Helper\Data $priceHelper,
        \Webkul\StorePickup\Helper\Data $dataHelper,
        \Magento\Framework\DB\Adapter\AdapterInterface $connection = null,
        \Magento\Framework\Model\ResourceModel\Db\AbstractDb $resource = null
    ) {
        $this->collectionFactory = $collectionFactory;
        $this->priceHelper = $priceHelper;
        $this->dataHelper = $dataHelper;

        parent::__construct(
            $entityFactory,
            $logger,
            $fetchStrategy,
            $eventManager,
            $connection,
            $resource
        );
    }

    /**
     * Define resource model
     * @return void
     */
    protected function _construct()
    {
        $this->_init(
            \Webkul\StorePickup\Model\Stores::class,
            \Webkul\StorePickup\Model\ResourceModel\Stores::class
        );
    }

    /**
     * {@inheritdoc}
     */
    public function load($printQuery = false, $logQuery = false)
    {
        if ($this->isLoaded()) {
            return $this;
        }
        parent::load($printQuery, $logQuery);
        $this->doAddStoreDetailsToStoreCollection();
        $this->doAddStoreTimingsToStoreCollection();
        $this->doAddStoreHolidaysToStoreCollection();
        $this->doAddAssignedProductsToStoreCollection();
        return $this;
    }

    /**
     * Processs adding store details to store collection
     * @return $this
     */
    protected function doAddStoreDetailsToStoreCollection()
    {
        $pickupStores = [];
        $mainTable = $this->getResource()->getTable('webkul_storepickup_stores');
        $select = $this->getConnection()->select()
        ->from(
            ['main_table' => $mainTable]
        )
        ->reset(\Zend_Db_Select::COLUMNS)
        ->join(
            ['stores_details' => $this->getTable('webkul_storepickup_details')],
            'main_table.entity_id = stores_details.store_id',
            ['stores_details.*']
        );

        $data = $this->getConnection()->fetchAll($select);

        foreach ($data as $row) {
            $pickupStores[$row['store_id']] = $row;
        }

        foreach ($this as $pickupStore) {
            if (isset($pickupStores[$pickupStore->getId()])) {
                $pickupStore->setData('stores_details', $pickupStores[$pickupStore->getId()]);
            }
        }

        return $this;
    }

    /**
     * Processs adding store timings to store collection
     * @return $this
     */
    protected function doAddStoreTimingsToStoreCollection()
    {
        $pickupStores = [];
        $mainTable = $this->getResource()->getTable('webkul_storepickup_stores');
        $select = $this->getConnection()->select()
        ->from(
            ['main_table' => $mainTable]
        )
        ->reset(\Zend_Db_Select::COLUMNS)
        ->join(
            ['stores_timings' => $this->getTable('webkul_storepickup_timings')],
            'main_table.entity_id = stores_timings.store_id',
            ['stores_timings.*']
        );

        $data = $this->getConnection()->fetchAll($select);

        foreach ($data as $row) {
            if ($row['timings']) {
                $row['timings'] = $this->dataHelper->unserialize($row['timings']);
            }
            $pickupStores[$row['store_id']] = $row;
        }

        foreach ($this as $pickupStore) {
            if (isset($pickupStores[$pickupStore->getId()])) {
                $pickupStore->setData('stores_timings', $pickupStores[$pickupStore->getId()]);
            }
        }

        return $this;
    }

    /**
     * Processs adding store holidays to store collection
     * @return $this
     */
    protected function doAddStoreHolidaysToStoreCollection()
    {
        $pickupStores = [];
        $mainTable = $this->getResource()->getTable('webkul_storepickup_stores');
        $select = $this->getConnection()->select()
        ->from(
            ['main_table' => $mainTable]
        )
        ->reset(\Zend_Db_Select::COLUMNS)
        ->join(
            ['stores_holidays' => $this->getTable('webkul_storepickup_stores_holidays_relation')],
            'main_table.entity_id = stores_holidays.store_id',
            ['stores_holidays.*']
        );

        $data = $this->getConnection()->fetchAll($select);

        foreach ($data as $row) {
            $pickupStores[$row['store_id']][] = $row['holiday_id'];
        }

        foreach ($this as $pickupStore) {
            if (isset($pickupStores[$pickupStore->getId()])) {
                $temp = [];
                $temp['holidays'] = $pickupStores[$pickupStore->getId()];
                $pickupStore->setData('stores_holidays', $temp);
            }
        }

        return $this;
    }

    /**
     * Processs adding assigned products to store collection
     * @return $this
     */
    protected function doAddAssignedProductsToStoreCollection()
    {
        $mainTable = $this->getResource()->getTable('webkul_storepickup_stores');
        $select = $this->getConnection()->select()
        ->from(
            ['main_table' => $mainTable]
        )
        ->reset(\Zend_Db_Select::COLUMNS)
        ->join(
            ['relation' => $this->getTable('webkul_storepickup_stores_products_relation')],
            'main_table.entity_id = relation.store_id',
            ['relation.*']
        )
        ->distinct(true);

        $data = [];
        $result = $this->getConnection()->fetchAll($select);
        foreach ($result as $row) {
            $data[] = [
                'product_id' => $row['product_id'],
                'store_id' => $row['store_id'],
                'qty' => $row['qty']
            ];
        }

        $productIds = array_unique(array_column($data, 'product_id'));

        $assignedProductData = [];
        if (!empty($productIds)) {
            $collection = $this->collectionFactory->create()
                ->addAttributeToSelect(['name', 'status', 'price'])
                ->addFieldToFilter('entity_id', ['in' => $productIds]);

            foreach ($collection as $product) {
                foreach ($data as $relation) {
                    if ($relation['product_id'] == $product->getId()) {

                        $product->setPrice(
                            $this->priceHelper->currency(
                                $product->getPrice(),
                                true,
                                false
                            )
                        );

                        $product->setQuantity($relation['qty']);
                        $assignedProductData[$relation['store_id']][] = $product->getData();
                    }
                }
            }
        }

        foreach ($this as $pickupStore) {
            if (isset($assignedProductData[$pickupStore->getId()])) {
                $pickupStore->setData('assigned_products', $assignedProductData[$pickupStore->getId()]);
            }
        }

        return $this;
    }
}
