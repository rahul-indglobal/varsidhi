<?php

namespace Varsidhi\Homepage\Model\Category\Source;

use Magento\Framework\Data\OptionSourceInterface;
use Magento\Catalog\Model\ResourceModel\Category\CollectionFactory;
use Magento\Store\Model\StoreManagerInterface;

class CategoryList implements OptionSourceInterface
{
    /**
     * @var CollectionFactory
     */
    protected $categoryCollectionFactory;

    /**
     * @var StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var array
     */
    protected $options;

    /**
     * @param CollectionFactory $categoryCollectionFactory
     * @param StoreManagerInterface $storeManager
     */
    public function __construct(
        CollectionFactory $categoryCollectionFactory,
        StoreManagerInterface $storeManager
    ) {
        $this->categoryCollectionFactory = $categoryCollectionFactory;
        $this->storeManager = $storeManager;
    }

    /**
     * Get all categories as options
     *
     * @return array
     */
    public function toOptionArray()
    {
        if ($this->options === null) {
            $collection = $this->categoryCollectionFactory->create();
            $collection->addAttributeToSelect('name')
                ->addAttributeToFilter('is_active', 1)
                ->setStoreId($this->storeManager->getStore()->getId())
                ->addAttributeToFilter('level', ['gt' => 1]) // Skip Root Catalog
                ->addAttributeToSort('path', 'asc');

            $options = [];
            foreach ($collection as $category) {
                $level = $category->getLevel();
                $prefix = str_repeat('- ', ($level - 2) * 2);
                $options[] = [
                    'label' => $prefix . $category->getName(),
                    'value' => $category->getId(),
                ];
            }
            $this->options = $options;
        }
        return $this->options;
    }
}
