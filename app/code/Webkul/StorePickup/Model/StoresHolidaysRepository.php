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

namespace Webkul\StorePickup\Model;

use Webkul\StorePickup\Api\StoresHolidaysRepositoryInterface;
use Webkul\StorePickup\Api\Data\StoresHolidaysSearchResultsInterfaceFactory;
use Webkul\StorePickup\Api\Data\StoresHolidaysInterfaceFactory;
use Magento\Framework\Api\DataObjectHelper;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Reflection\DataObjectProcessor;
use Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface;
use Webkul\StorePickup\Model\ResourceModel\StoresHolidays as ResourceStoresHolidays;
use Webkul\StorePickup\Model\ResourceModel\StoresHolidays\CollectionFactory as StoresHolidaysCollectionFactory;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\Api\ExtensionAttribute\JoinProcessorInterface;
use Magento\Framework\Api\ExtensibleDataObjectConverter;

class StoresHolidaysRepository implements StoresHolidaysRepositoryInterface
{
    /**
     * @var $resource
     */
    protected $resource;

    /**
     * @var $storesHolidaysFactory
     */
    protected $storesHolidaysFactory;

    /**
     * @var $storesHolidaysCollectionFactory
     */
    protected $storesHolidaysCollectionFactory;

    /**
     * @var $searchResultsFactory
     */
    protected $searchResultsFactory;

    /**
     * @var $dataObjectHelper
     */
    protected $dataObjectHelper;

    /**
     * @var $dataObjectProcessor
     */
    protected $dataObjectProcessor;

    /**
     * @var $dataStoresHolidaysFactory
     */
    protected $dataStoresHolidaysFactory;

    /**
     * @var $extensionAttributesJoinProcessor
     */
    protected $extensionAttributesJoinProcessor;

    /**
     * @var $storeManager
     */
    private $storeManager;

    /**
     * @var $collectionProcessor
     */
    private $collectionProcessor;

    /**
     * @var $extensibleDataObjectConverter
     */
    protected $extensibleDataObjectConverter;

    /**
     * @param ResourceStoresHolidays $resource
     * @param StoresHolidaysFactory $storesHolidaysFactory
     * @param StoresHolidaysInterfaceFactory $dataStoresHolidaysFactory
     * @param StoresHolidaysCollectionFactory $storesHolidaysCollectionFactory
     * @param StoresHolidaysSearchResultsInterfaceFactory $searchResultsFactory
     * @param DataObjectHelper $dataObjectHelper
     * @param DataObjectProcessor $dataObjectProcessor
     * @param StoreManagerInterface $storeManager
     * @param CollectionProcessorInterface $collectionProcessor
     * @param JoinProcessorInterface $extensionAttributesJoinProcessor
     * @param ExtensibleDataObjectConverter $extensibleDataObjectConverter
     */
    public function __construct(
        ResourceStoresHolidays $resource,
        StoresHolidaysFactory $storesHolidaysFactory,
        StoresHolidaysInterfaceFactory $dataStoresHolidaysFactory,
        StoresHolidaysCollectionFactory $storesHolidaysCollectionFactory,
        StoresHolidaysSearchResultsInterfaceFactory $searchResultsFactory,
        DataObjectHelper $dataObjectHelper,
        DataObjectProcessor $dataObjectProcessor,
        StoreManagerInterface $storeManager,
        CollectionProcessorInterface $collectionProcessor,
        JoinProcessorInterface $extensionAttributesJoinProcessor,
        ExtensibleDataObjectConverter $extensibleDataObjectConverter
    ) {
        $this->resource = $resource;
        $this->storesHolidaysFactory = $storesHolidaysFactory;
        $this->storesHolidaysCollectionFactory = $storesHolidaysCollectionFactory;
        $this->searchResultsFactory = $searchResultsFactory;
        $this->dataObjectHelper = $dataObjectHelper;
        $this->dataStoresHolidaysFactory = $dataStoresHolidaysFactory;
        $this->dataObjectProcessor = $dataObjectProcessor;
        $this->storeManager = $storeManager;
        $this->collectionProcessor = $collectionProcessor;
        $this->extensionAttributesJoinProcessor = $extensionAttributesJoinProcessor;
        $this->extensibleDataObjectConverter = $extensibleDataObjectConverter;
    }

    /**
     * {@inheritdoc}
     */
    public function save(
        \Webkul\StorePickup\Api\Data\StoresHolidaysInterface $storesHolidays
    ) {
        $storesHolidaysData = $this->extensibleDataObjectConverter->toNestedArray(
            $storesHolidays,
            [],
            \Webkul\StorePickup\Api\Data\StoresHolidaysInterface::class
        );

        $storesHolidaysModel = $this->storesHolidaysFactory->create()->setData($storesHolidaysData);

        try {
            $this->resource->save($storesHolidaysModel);
        } catch (\Exception $exception) {
            throw new CouldNotSaveException(__(
                'Could not save the storesHolidays: %1',
                $exception->getMessage()
            ));
        }
        return $storesHolidaysModel;
    }

    /**
     * {@inheritdoc}
     */
    public function get($entityId)
    {
        $storesHolidays = $this->storesHolidaysFactory->create();
        $this->resource->load($storesHolidays, $entityId);
        if (!$storesHolidays->getId()) {
            throw new NoSuchEntityException(__('StoresHolidays with id "%1" does not exist.', $entityId));
        }
        return $storesHolidays;
    }

    /**
     * {@inheritdoc}
     */
    public function getList(
        \Magento\Framework\Api\SearchCriteriaInterface $criteria
    ) {
        $collection = $this->storesHolidaysCollectionFactory->create();

        $this->extensionAttributesJoinProcessor->process(
            $collection,
            \Webkul\StorePickup\Api\Data\StoresHolidaysInterface::class
        );

        $this->collectionProcessor->process($criteria, $collection);

        $searchResults = $this->searchResultsFactory->create();
        $searchResults->setSearchCriteria($criteria);

        $items = [];
        foreach ($collection as $model) {
            $items[] = $model;
        }

        $searchResults->setItems($items);
        $searchResults->setTotalCount($collection->getSize());
        return $searchResults;
    }

    /**
     * {@inheritdoc}
     */
    public function delete(
        \Webkul\StorePickup\Api\Data\StoresHolidaysInterface $storesHolidays
    ) {
        try {
            $storesHolidaysModel = $this->storesHolidaysFactory->create();
            $this->resource->load($storesHolidaysModel, $storesHolidays->getHolidayId());
            $this->resource->delete($storesHolidaysModel);
        } catch (\Exception $exception) {
            throw new CouldNotDeleteException(__(
                'Could not delete the StoresHolidays: %1',
                $exception->getMessage()
            ));
        }
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function deleteById($entityId)
    {
        return $this->delete($this->get($entityId));
    }
}
