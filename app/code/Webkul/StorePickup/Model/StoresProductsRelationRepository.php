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
use Webkul\StorePickup\Api\Data\StoresProductsRelationInterfaceFactory;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\Api\DataObjectHelper;
use Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface;
use Webkul\StorePickup\Api\Data\StoresProductsRelationSearchResultsInterfaceFactory;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Reflection\DataObjectProcessor;
use Magento\Framework\Api\ExtensibleDataObjectConverter;
use Webkul\StorePickup\Api\StoresProductsRelationRepositoryInterface;
use Webkul\StorePickup\Model\ResourceModel\StoresProductsRelation\CollectionFactory
    as StoresProductsRelationCollectionFactory;
use Webkul\StorePickup\Model\ResourceModel\StoresProductsRelation as ResourceStoresProductsRelation;
use Magento\Framework\Exception\NoSuchEntityException;

class StoresProductsRelationRepository implements StoresProductsRelationRepositoryInterface
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
     * @var $storesProductsRelationCollectionFactory
     */
    protected $storesProductsRelationCollectionFactory;

    /**
     * @var $storesProductsRelationFactory
     */
    protected $storesProductsRelationFactory;

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
     * @var $dataStoresProductsRelationFactory
     */
    protected $dataStoresProductsRelationFactory;

    /**
     * @param ResourceStoresProductsRelation $resource
     * @param StoresProductsRelationFactory $storesProductsRelationFactory
     * @param StoresProductsRelationInterfaceFactory $dataStoresProductsRelationFactory
     * @param StoresProductsRelationCollectionFactory $storesProductsRelationCollectionFactory
     * @param StoresProductsRelationSearchResultsInterfaceFactory $searchResultsFactory
     * @param DataObjectHelper $dataObjectHelper
     * @param DataObjectProcessor $dataObjectProcessor
     * @param StoreManagerInterface $storeManager
     * @param CollectionProcessorInterface $collectionProcessor
     * @param JoinProcessorInterface $extensionAttributesJoinProcessor
     * @param ExtensibleDataObjectConverter $extensibleDataObjectConverter
     */
    public function __construct(
        ResourceStoresProductsRelation $resource,
        StoresProductsRelationFactory $storesProductsRelationFactory,
        StoresProductsRelationInterfaceFactory $dataStoresProductsRelationFactory,
        StoresProductsRelationCollectionFactory $storesProductsRelationCollectionFactory,
        StoresProductsRelationSearchResultsInterfaceFactory $searchResultsFactory,
        DataObjectHelper $dataObjectHelper,
        DataObjectProcessor $dataObjectProcessor,
        StoreManagerInterface $storeManager,
        CollectionProcessorInterface $collectionProcessor,
        JoinProcessorInterface $extensionAttributesJoinProcessor,
        ExtensibleDataObjectConverter $extensibleDataObjectConverter
    ) {
        $this->resource = $resource;
        $this->storesProductsRelationFactory = $storesProductsRelationFactory;
        $this->storesProductsRelationCollectionFactory = $storesProductsRelationCollectionFactory;
        $this->searchResultsFactory = $searchResultsFactory;
        $this->dataObjectHelper = $dataObjectHelper;
        $this->dataStoresProductsRelationFactory = $dataStoresProductsRelationFactory;
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
        \Webkul\StorePickup\Api\Data\StoresProductsRelationInterface $storesProductsRelation
    ) {
        $storesProductsRelationData = $this->extensibleDataObjectConverter->toNestedArray(
            $storesProductsRelation,
            [],
            \Webkul\StorePickup\Api\Data\StoresProductsRelationInterface::class
        );

        $storesProductsRelationModel = $this->storesProductsRelationFactory->create();
        $storesProductsRelationModel->setData($storesProductsRelationData);
        try {
            $this->resource->save($storesProductsRelationModel);
        } catch (\Exception $exception) {
            throw new CouldNotSaveException(__(
                'Could not save the storesProductsRelation: %1',
                $exception->getMessage()
            ));
        }
        return $storesProductsRelationModel;
    }

    /**
     * {@inheritdoc}
     */
    public function get($storesProductsRelationId)
    {
        $storesProductsRelation = $this->storesProductsRelationFactory->create();
        $this->resource->load($storesProductsRelation, $storesProductsRelationId);
        if (!$storesProductsRelation->getId()) {
            throw new NoSuchEntityException(
                __('StoresProductsRelation with id "%1" does not exist.', $storesProductsRelationId)
            );
        }
        return $storesProductsRelation;
    }

    /**
     * {@inheritdoc}
     */
    public function getList(
        \Magento\Framework\Api\SearchCriteriaInterface $criteria
    ) {
        $collection = $this->storesProductsRelationCollectionFactory->create();

        $this->extensionAttributesJoinProcessor->process(
            $collection,
            \Webkul\StorePickup\Api\Data\StoresProductsRelationInterface::class
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
        \Webkul\StorePickup\Api\Data\StoresProductsRelationInterface $storesProductsRelation
    ) {
        try {
            $storesProductsRelationModel = $this->storesProductsRelationFactory->create();
            $this->resource->load($storesProductsRelationModel, $storesProductsRelation->getStoresproductsrelationId());
            $this->resource->delete($storesProductsRelationModel);
        } catch (\Exception $exception) {
            throw new CouldNotDeleteException(__(
                'Could not delete the StoresProductsRelation: %1',
                $exception->getMessage()
            ));
        }
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function deleteById($storesProductsRelationId)
    {
        return $this->delete($this->get($storesProductsRelationId));
    }
}
