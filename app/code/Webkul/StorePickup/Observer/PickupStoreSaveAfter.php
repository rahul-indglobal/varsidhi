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

namespace Webkul\StorePickup\Observer;

use Magento\Framework\Event\ObserverInterface;

class PickupStoreSaveAfter implements ObserverInterface
{
    /**
     * @var $request
     */
    protected $request;

    /**
     * @var $dataHelper
     */
    protected $dataHelper;

    /**
     * @var $storesProductRelationFactory
     */
    protected $storesProductRelationFactory;

    /**
     * @var $categoryFactory
     */
    protected $categoryFactory;

    /**
     * @var $blockFactory
     */
    protected $blockFactory;

    /**
     * @var $storeManager
     */
    protected $storeManager;

    /**
     * Constructor
     * @param Magento\Framework\App\RequestInterface                  $request
     * @param Webkul\StorePickup\Helper\Data                          $dataHelper
     * @param Webkul\StorePickup\Model\Stores\ProductsRelationFactory $storesProductRelationFactory
     * @param Magento\Catalog\Model\CategoryFactory                   $categoryFactory
     * @param Magento\Cms\Model\BlockFactory                          $blockFactory
     * @param Magento\Store\Model\StoreManagerInterface               $storeManager
     */
    public function __construct(
        \Magento\Framework\App\RequestInterface $request,
        \Webkul\StorePickup\Helper\Data $dataHelper,
        \Webkul\StorePickup\Model\StoresProductsRelationFactory $storesProductRelationFactory,
        \Magento\Catalog\Model\CategoryFactory $categoryFactory,
        \Magento\Cms\Model\BlockFactory $blockFactory,
        \Magento\Store\Model\StoreManagerInterface $storeManager
    ) {
        $this->request = $request;
        $this->dataHelper = $dataHelper;
        $this->storesProductRelationFactory = $storesProductRelationFactory;
        $this->categoryFactory = $categoryFactory;
        $this->blockFactory = $blockFactory;
        $this->storeManager = $storeManager;
    }

    /**
     * execute
     * @param \Magento\Framework\Event\Observer $observer
     * @return void
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $store = $observer->getPickupStore();
        $data = $store->getData();

        if (!empty($data['entity_id'])) {
            if (!empty($data['product_assignment']['assign_products_grid'])) {
                $productIds = array_column($data['product_assignment']['assign_products_grid'], 'entity_id');
            }

            $urlKey = 'pickup-store-'.$data['entity_id'];
            $name = $data['name'];
            $categoryId = $this->getCategoryId($urlKey);

            if (!$categoryId) {
                $this->createPickupStoreCategory($urlKey, $name, $categoryId, $data);
            } else {
                $this->doEnableDisableCategory($categoryId, $data);
                $this->assignProductsToCategory($data, $categoryId);
            }
        }
    }

    /**
     * do enable disbale category
     * @param int $categoryId
     * @param object $data
     * @return void
     */
    private function doEnableDisableCategory($categoryId, $data)
    {
        $category = $this->categoryFactory->create()->load($categoryId);
        if ($data['is_enabled'] != $category->getIsActive()) {
            $category->setIsActive($data['is_enabled']);
            $category->save();
            $category->setStoreId(0);
            $category->save();
        }
    }

    /**
     * create Pickup Store Category
     * @param string $urlKey
     * @param string $name
     * @param int $categoryId
     * @param array $data
     * @return void
     */
    private function createPickupStoreCategory($urlKey, $name, $categoryId, $data)
    {
        $blockId = $this->getPickupStoresDetailsCMSBlockId();
        if ($blockId) {
            $newCategoryId = $this->doCreatePickupStoreCategory($blockId, $urlKey, $name, $categoryId);
            if ($newCategoryId) {
                $this->assignProductsToCategory($data, $newCategoryId);
            }
        }
    }

    /**
     * assign Products To Category
     * @param array $data
     * @param int $categoryId
     * @return void
     */
    private function assignProductsToCategory($data, $categoryId)
    {
        $productIds = [];
        if (!empty($data['product_assignment']['assigned_products'])) {
            $assignedProducts = $data['product_assignment']['assigned_products'];
            $productIds = array_column($assignedProducts, 'entity_id');
        }

        $storesRelation = $this->storesProductRelationFactory->create();
        $storesRelation->saveAssignedProductsToCategory($productIds, $categoryId);
    }

    /**
     * so create pickup store category
     * @param int $blockId
     * @param string $urlKey
     * @param string $name
     * @param int $categoryId
     * @return int
     */
    private function doCreatePickupStoreCategory($blockId, $urlKey, $name, $categoryId)
    {
        $store = $this->storeManager->getStore();
        $storeId = $store->getId();

        $pickupStoresCategoryDetails = $this->getPickupStoresCategoryDetails();

        if (!empty($pickupStoresCategoryDetails)) {
            $category = $this->categoryFactory->create();
            $category->setName($name);
            $category->setIsActive(true);
            $category->setUrlKey($urlKey);
            $category->setData('landing_page', $blockId);
            $category->setData('display_mode', \Magento\Catalog\Model\Category::DM_MIXED);
            $category->setParentId($pickupStoresCategoryDetails['id']);
            $category->setStoreId($storeId);
            $category->setPath($pickupStoresCategoryDetails['path']);
            $model = $category->save();
            return $model->getId();
        }

        return false;
    }

    /**
     * get Pickup Stores Category Details
     * @param void
     * @return array
     */
    private function getPickupStoresCategoryDetails()
    {
        $collection = $this->categoryFactory->create()->getCollection()
            ->addFieldToFilter('url_key', ['eq' => 'pickup-stores']);

        $data = [];

        foreach ($collection as $category) {
            $data = [
                'id' => $category->getId(),
                'path' => $category->getPath()
            ];
        }

        return $data;
    }

    /**
     * get Pickup Stores Details CMS Block Id
     * @param void
     * @return mixed
     */
    private function getPickupStoresDetailsCMSBlockId()
    {
        $collection = $this->blockFactory->create()->getCollection()
            ->addFieldToFilter('identifier', ['eq' => 'pickup-stores-details']);

        foreach ($collection as $block) {
            return $block->getId();
        }

        return false;
    }

    /**
     * get Category Id
     * @param string $urlKey
     * @return int
     */
    private function getCategoryId($urlKey)
    {
        $collection = $this->categoryFactory->create()->getCollection()
            ->addFieldToFilter('url_key', ['eq' => $urlKey]);

        $categoryId = 0;
        if ($collection->getSize()) {
            foreach ($collection as $category) {
                $categoryId = $category->getId();
            }
        }

        return $categoryId;
    }
}
