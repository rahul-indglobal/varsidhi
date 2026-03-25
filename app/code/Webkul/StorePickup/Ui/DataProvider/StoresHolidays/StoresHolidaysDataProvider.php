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

namespace Webkul\StorePickup\Ui\DataProvider\StoresHolidays;

use Webkul\StorePickup\Model\ResourceModel\StoresHolidays\CollectionFactory;

class StoresHolidaysDataProvider extends \Magento\Ui\DataProvider\AbstractDataProvider
{
    /**
     * Holidays collection
     * @var \Magento\Sales\Model\ResourceModel\Order\Collection
     */
    protected $collection;

    /**
     * @var \Magento\Ui\DataProvider\AddFieldToCollectionInterface[]
     */
    protected $addFieldStrategies;

    /**
     * @var \Magento\Ui\DataProvider\AddFilterToCollectionInterface[]
     */
    protected $addFilterStrategies;

    /**
     * @param string $name
     * @param string $primaryFieldName
     * @param string $requestFieldName
     * @param CollectionFactory $collectionFactory
     * @param \Magento\Ui\DataProvider\AddFieldToCollectionInterface[] $addFieldStrategies
     * @param \Magento\Ui\DataProvider\AddFilterToCollectionInterface[] $addFilterStrategies
     * @param array $meta
     * @param array $data
     */
    public function __construct(
        $name,
        $primaryFieldName,
        $requestFieldName,
        CollectionFactory $collectionFactory,
        array $addFieldStrategies = [],
        array $addFilterStrategies = [],
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
        $this->addFieldStrategies = $addFieldStrategies;
        $this->addFilterStrategies = $addFilterStrategies;
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

        $collection = $this->getCollection();
        $data = [
            'totalRecords' => $collection->getSize(),
            'items' => $collection->getData(),
        ];

        return $data;
    }
}
