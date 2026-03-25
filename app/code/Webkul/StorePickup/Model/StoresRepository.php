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

use Magento\Framework\Api\ExtensionAttribute\JoinProcessorInterface;
use Magento\Framework\Exception\CouldNotSaveException;
use Webkul\StorePickup\Model\ResourceModel\Stores as ResourceStores;
use Webkul\StorePickup\Model\ResourceModel\Stores\CollectionFactory as StoresCollectionFactory;
use Magento\Store\Model\StoreManagerInterface;
use Webkul\StorePickup\Api\Data\StoresInterfaceFactory;
use Magento\Framework\Api\DataObjectHelper;
use Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface;
use Magento\Framework\Exception\CouldNotDeleteException;
use Webkul\StorePickup\Api\StoresRepositoryInterface;
use Webkul\StorePickup\Api\Data\StoresSearchResultsInterfaceFactory;
use Magento\Framework\Reflection\DataObjectProcessor;
use Magento\Framework\Api\ExtensibleDataObjectConverter;
use Magento\Framework\Exception\NoSuchEntityException;

class StoresRepository implements StoresRepositoryInterface
{
    /**
     * @var $dataObjectHelper
     */
    protected $dataObjectHelper;

    /**
     * @var $storeManager
     */
    private $storeManager;

    /**
     * @var $searchResultsFactory
     */
    protected $searchResultsFactory;

    /**
     * @var $dataObjectProcessor
     */
    protected $dataObjectProcessor;

    /**
     * @var $extensionAttributesJoinProcessor
     */
    protected $extensionAttributesJoinProcessor;

    /**
     * @var $collectionProcessor
     */
    private $collectionProcessor;

    /**
     * @var $extensibleDataObjectConverter
     */
    protected $extensibleDataObjectConverter;

    /**
     * @var $resource
     */
    protected $resource;

    /**
     * @var $storesCollectionFactory
     */
    protected $storesCollectionFactory;

    /**
     * @var $storesFactory
     */
    protected $storesFactory;

    /**
     * @var $dataStoresFactory
     */
    protected $dataStoresFactory;

    /**
     * @param ResourceStores $resource
     * @param StoresFactory $storesFactory
     * @param StoresInterfaceFactory $dataStoresFactory
     * @param StoresCollectionFactory $storesCollectionFactory
     * @param StoresSearchResultsInterfaceFactory $searchResultsFactory
     * @param DataObjectHelper $dataObjectHelper
     * @param DataObjectProcessor $dataObjectProcessor
     * @param StoreManagerInterface $storeManager
     * @param CollectionProcessorInterface $collectionProcessor
     * @param JoinProcessorInterface $extensionAttributesJoinProcessor
     * @param ExtensibleDataObjectConverter $extensibleDataObjectConverter
     */
    public function __construct(
        ResourceStores $resource,
        StoresFactory $storesFactory,
        StoresInterfaceFactory $dataStoresFactory,
        StoresCollectionFactory $storesCollectionFactory,
        StoresSearchResultsInterfaceFactory $searchResultsFactory,
        DataObjectHelper $dataObjectHelper,
        DataObjectProcessor $dataObjectProcessor,
        StoreManagerInterface $storeManager,
        CollectionProcessorInterface $collectionProcessor,
        JoinProcessorInterface $extensionAttributesJoinProcessor,
        ExtensibleDataObjectConverter $extensibleDataObjectConverter
    ) {
        $this->resource = $resource;
        $this->storesFactory = $storesFactory;
        $this->storesCollectionFactory = $storesCollectionFactory;
        $this->searchResultsFactory = $searchResultsFactory;
        $this->dataObjectHelper = $dataObjectHelper;
        $this->dataStoresFactory = $dataStoresFactory;
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
        \Webkul\StorePickup\Api\Data\StoresInterface $stores
    ) {
        $storesData = $this->extensibleDataObjectConverter->toNestedArray(
            $stores,
            [],
            \Webkul\StorePickup\Api\Data\StoresInterface::class
        );

        $storesModel = $this->storesFactory->create()->setData($storesData);

        try {
            $this->resource->save($storesModel);
        } catch (\Exception $exception) {
            throw new CouldNotSaveException(__(
                'Could not save the stores: %1',
                $exception->getMessage()
            ));
        }
        return $storesModel;
    }

    /**
     * {@inheritdoc}
     */
    public function get($storesId)
    {
        $stores = $this->storesFactory->create();
        $this->resource->load($stores, $storesId);
        if (!$stores->getId()) {
            throw new NoSuchEntityException(__('Stores with id "%1" does not exist.', $storesId));
        }
        return $stores;
    }

    /**
     * {@inheritdoc}
     */
    public function getList(
        \Magento\Framework\Api\SearchCriteriaInterface $criteria
    ) {
        $collection = $this->storesCollectionFactory->create();

        $this->extensionAttributesJoinProcessor->process(
            $collection,
            \Webkul\StorePickup\Api\Data\StoresInterface::class
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
        \Webkul\StorePickup\Api\Data\StoresInterface $stores
    ) {
        try {
            $storesModel = $this->storesFactory->create();
            $this->resource->load($storesModel, $stores->getEntityId());
            $this->resource->delete($storesModel);
        } catch (\Exception $exception) {
            throw new CouldNotDeleteException(__(
                'Could not delete the Stores: %1',
                $exception->getMessage()
            ));
        }
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function deleteById($storesId)
    {
        return $this->delete($this->get($storesId));
    }
}
