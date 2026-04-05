<?php

namespace Varsidhi\CategoryGrid\Block;

use Magento\Framework\View\Element\Template;
use Magento\Catalog\Model\ResourceModel\Category\CollectionFactory;
use Magento\Catalog\Helper\ImageFactory;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Catalog\Model\CategoryRepository;

class CategoryGrid extends Template
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
     * @var CategoryRepository
     */
    protected $categoryRepository;

    /**
     * @param Template\Context $context
     * @param CollectionFactory $categoryCollectionFactory
     * @param StoreManagerInterface $storeManager
     * @param CategoryRepository $categoryRepository
     * @param array $data
     */
    public function __construct(
        Template\Context $context,
        CollectionFactory $categoryCollectionFactory,
        StoreManagerInterface $storeManager,
        CategoryRepository $categoryRepository,
        array $data = []
    ) {
        $this->categoryCollectionFactory = $categoryCollectionFactory;
        $this->storeManager = $storeManager;
        $this->categoryRepository = $categoryRepository;
        parent::__construct($context, $data);
    }

    /**
     * Get Category Data
     *
     * @return array
     */
    public function getCategoryData()
    {
        $categoryIds = $this->getData('category_ids');
        if (empty($categoryIds)) {
            return [];
        }

        if (is_string($categoryIds)) {
            $categoryIds = explode(',', $categoryIds);
        }

        if (!is_array($categoryIds)) {
            return [];
        }

        $categoryIds = array_filter($categoryIds);

        if (empty($categoryIds)) {
            return [];
        }

        $collection = $this->categoryCollectionFactory->create();
        $collection->addAttributeToSelect('*')
            ->addAttributeToFilter('entity_id', ['in' => $categoryIds])
            ->addAttributeToFilter('is_active', 1)
            ->setStoreId($this->storeManager->getStore()->getId());
        
        // Ensure categories are in the same order as in the comma-separated list
        $categoryIdsString = implode(',', array_map('intval', $categoryIds));
        $collection->getSelect()->order(new \Zend_Db_Expr("FIELD(e.entity_id, " . $categoryIdsString . ")"));

        $categoryData = [];
        foreach ($collection as $category) {
            $categoryData[] = [
                'name' => $category->getName(),
                'url' => $category->getUrl(),
                'image' => $this->getCustomCategoryImage($category),
                'product_count' => $category->getProductCount(),
            ];
        }

        return $categoryData;
    }

    /**
     * Get Custom Category Image URL
     *
     * @param \Magento\Catalog\Model\Category $category
     * @return string|bool
     */
    public function getCustomCategoryImage($category)
    {
        $image = $category->getCustomCategoryImage();
	    if ($image) {
		    if (is_string($image)) {
			    // Check if $image already contains the catalog/category path
			    if (strpos($image, 'catalog/category') !== false) {
				    return $this->storeManager->getStore()->getBaseUrl() . ltrim($image, '/');
			    }

			    // Otherwise, append the directory manually
			    return $this->storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA) . 'catalog/category/' . ltrim($image, '/');
		    }
	    }
	    return false;
    }
}
