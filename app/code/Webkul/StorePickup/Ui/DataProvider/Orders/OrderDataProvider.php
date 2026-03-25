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

namespace Webkul\StorePickup\Ui\DataProvider\Orders;

use Magento\Sales\Model\ResourceModel\Order\CollectionFactory;

class OrderDataProvider extends \Magento\Ui\DataProvider\AbstractDataProvider
{
    /**
     * Order collection
     * @var \Magento\Sales\Model\ResourceModel\Order\Collection
     */
    protected $collection;

    /**
     * @param string $name
     * @param string $primaryFieldName
     * @param string $requestFieldName
     * @param CollectionFactory $collectionFactory
     * @param array $meta
     * @param array $data
     */
    public function __construct(
        $name,
        $primaryFieldName,
        $requestFieldName,
        CollectionFactory $collectionFactory,
        array $meta = [],
        array $data = []
    ) {
        parent::__construct(
            $name,
            $primaryFieldName,
            $requestFieldName,
            $meta,
            $data
        );

        $this->init($collectionFactory);
    }

    /**
     * init method
     * @param CollectionFactory $collectionFactory
     * @return void
     */
    public function init($collectionFactory)
    {
        $this->collection = $collectionFactory->create();
        $collection = $collectionFactory->create();
        $this->collection = $collection;
    }

    /**
     * Get data
     * @return array
     */
    public function getData()
    {
        if (!$this->getCollection()->isLoaded()) {
            $this->getCollection()->load();
        }

        $collection = $this->getCollection()
            ->addFieldToFilter('pickup_store', ['notnull' => true]);

        $data = [
            'totalRecords' => count($collection->getData()),
            'items' => $collection->getData(),
        ];

        return $data;
    }
}
