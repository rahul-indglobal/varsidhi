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

namespace Webkul\StorePickup\Ui\DataProvider\Product;

use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory;
use Magento\Store\Model\Store;

/**
 * Products DataProvider
 */
class ProductDataProvider extends \Magento\Ui\DataProvider\AbstractDataProvider
{
    /**
     * Product collection
     * @var \Magento\Catalog\Model\ResourceModel\Product\Collection
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
        $collection->addAttributeToSelect('*');
        $collection->addFieldToFilter('type_id', ['eq' => 'simple']);
        $this->collection = $collection;
        $this->collection->setStoreId(Store::DEFAULT_STORE_ID);
    }

    /**
     * Get data
     * @param void
     * @return array
     */
    public function getData()
    {
        if (!$this->getCollection()->isLoaded()) {
            $this->getCollection()->load();
        }
        $items = $this->getCollection()->toArray();

        $data = [
            'totalRecords' => $this->getCollection()->getSize(),
            'items' => array_values($items),
        ];

        return $data;
    }
}
