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

namespace Webkul\StorePickup\Ui\DataProvider\Stores\Form;

use Webkul\StorePickup\Model\ResourceModel\Stores\CollectionFactory;
use Magento\Framework\App\Request\DataPersistorInterface;
use Magento\Framework\Registry;

class DataProvider extends \Magento\Ui\DataProvider\AbstractDataProvider
{
    /**
     * @var $collection
     */
    protected $collection;

    /**
     * @var $dataPersistor
     */
    protected $dataPersistor;

    /**
     * @var $loadedData
     */
    protected $loadedData;

    /**
     * @var $coreRegistry
     */
    protected $coreRegistry;

    /**
     * Constructor
     *
     * @param string $name
     * @param string $primaryFieldName
     * @param string $requestFieldName
     * @param CollectionFactory $collectionFactory
     * @param DataPersistorInterface $dataPersistor
     * @param Registry $coreRegistry
     * @param array $meta
     * @param array $data
     */
    public function __construct(
        $name,
        $primaryFieldName,
        $requestFieldName,
        CollectionFactory $collectionFactory,
        DataPersistorInterface $dataPersistor,
        Registry $coreRegistry,
        array $meta = [],
        array $data = []
    ) {
        $this->collection = $collectionFactory->create();
        $this->dataPersistor = $dataPersistor;
        $this->coreRegistry = $coreRegistry;
        parent::__construct($name, $primaryFieldName, $requestFieldName, $meta, $data);
    }

    /**
     * Get data
     * @return array
     */
    public function getData()
    {
        if (isset($this->loadedData)) {
            return $this->loadedData;
        }

        $items = $this->collection->getItems();
        foreach ($items as $model) {
            $data = $model->getData();
            if (isset($data['assigned_products'])) {
                $temp = $data['assigned_products'];
                unset($data['assigned_products']);
                $data['product_assignment']['assigned_products'] = $temp;
            }

            if (!isset($data['product_assignment']['assigned_products'])) {
                $coreData = $this->coreRegistry->registry('webkul_storepickup_stores');
                $data['product_assignment']['assigned_products'] = $coreData['product_assignment']['assigned_products'];
            }

            $this->loadedData[$model->getId()] = $data;
        }

        $data = $this->dataPersistor->get('webkul_storepickup_stores');
        if (!empty($data)) {
            $model = $this->collection->getNewEmptyItem();
            $model->setData($data);
            $this->loadedData[$model->getId()] = $model->getData();
            $this->dataPersistor->clear('webkul_storepickup_stores');
        }

        $this->dataPersistor->set('webkul_storepickup_stores_for_save_file', $this->loadedData);
        return $this->loadedData;
    }
}
