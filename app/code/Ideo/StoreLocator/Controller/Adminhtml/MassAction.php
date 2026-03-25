<?php
/**
 * Copyright © 2018 Ideo. All rights reserved.
 * @license GPLv3
 */

namespace Ideo\StoreLocator\Controller\Adminhtml;

use \Magento\Backend\App\Action;
use \Magento\Ui\Component\MassAction\Filter;
use \Ideo\StoreLocator\Model\ResourceModel\Store\CollectionFactory as StoreCollectionFactory;
use \Ideo\StoreLocator\Model\ResourceModel\Category\CollectionFactory as CategoryCollectionFactory;
use \Ideo\StoreLocator\Api\StoreRepositoryInterface;
use \Ideo\StoreLocator\Api\CategoryRepositoryInterface;

abstract class MassAction extends Action
{
    /**
     * @var \Magento\Ui\Component\MassAction\Filter
     */
    protected $filter;

    /**
     * @var \Ideo\StoreLocator\Model\ResourceModel\Store\CollectionFactory
     */
    protected $storeCollectionFactory;

    /**
     * @var \Ideo\StoreLocator\Model\ResourceModel\Category\CollectionFactory
     */
    protected $categoryCollectionFactory;

    /**
     * @var \Ideo\StoreLocator\Api\StoreRepositoryInterface
     */
    protected $storeRepository;

    /**
     * @var \Ideo\StoreLocator\Api\CategoryRepositoryInterface
     */
    protected $categoryRepository;

    /**
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Ui\Component\MassAction\Filter $filter
     * @param StoreCollectionFactory $storeCollectionFactory
     * @param CategoryCollectionFactory $categoryCollectionFactory
     * @param \Ideo\StoreLocator\Api\StoreRepositoryInterface $storeRepository
     * @param \Ideo\StoreLocator\Api\CategoryRepositoryInterface $categoryRepository
     * @internal param StoreCollectionFactory $collectionFactory
     * @internal param CategoryCollectionFactoryCategoryCollectionFactory $collectionFactory
     */
    public function __construct(
        Action\Context $context,
        Filter $filter,
        StoreCollectionFactory $storeCollectionFactory,
        CategoryCollectionFactory $categoryCollectionFactory,
        StoreRepositoryInterface $storeRepository,
        CategoryRepositoryInterface $categoryRepository
    ) {
        $this->filter = $filter;
        $this->storeCollectionFactory = $storeCollectionFactory;
        $this->categoryCollectionFactory = $categoryCollectionFactory;
        $this->storeRepository = $storeRepository;
        $this->categoryRepository= $categoryRepository;
        parent::__construct($context);
    }
}
