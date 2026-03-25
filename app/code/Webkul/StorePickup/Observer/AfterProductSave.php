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

class AfterProductSave implements ObserverInterface
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

    public function __construct(
        \Magento\Framework\App\RequestInterface $request,
        \Webkul\StorePickup\Helper\Data $dataHelper,
        \Webkul\StorePickup\Model\StoresProductsRelationFactory $storesProductRelationFactory,
        \Magento\Catalog\Model\CategoryFactory $categoryFactory
    ) {
        $this->request = $request;
        $this->dataHelper = $dataHelper;
        $this->storesProductRelationFactory = $storesProductRelationFactory;
        $this->categoryFactory = $categoryFactory;
    }

    /**
     * execute
     * @param \Magento\Framework\Event\Observer $observer
     * @return void
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $product = $observer->getProduct();
        $params = $this->request->getParams();

        $pickupStoreCatId = $this->getPickupStoresCategoryId();
        $allPickupCategories = $this->getAllPickupCategories($pickupStoreCatId);
        $pickupCategoryIds = $this->getCategoriesFromPickupStore($params, $pickupStoreCatId);
        $categories = $this->getCategoriesToBeDelete($allPickupCategories, $pickupCategoryIds);
        $productCategories = $product->getCategoryIds();

        $this->setProductCategories($productCategories, $categories, $product, $pickupCategoryIds);
        $this->savePickupCategories($allPickupCategories, $pickupCategoryIds, $product->getId());
        $this->savePickupStore($params, $product->getId());
    }

    /**
     * save pickup store
     * @param array $params
     * @param int $productId
     * @return void
     */
    private function savePickupStore($params, $productId)
    {
        $selectedData = [];
        if (!empty($params['links']['storepickup'])) {
            foreach ($params['links']['storepickup'] as $store) {
                $selectedData[$store['id']] = [
                    'store_id' => $store['id'],
                    'product_id' => $productId,
                    'qty' => 1
                ];
            }
        }

        $storeProductRelation = $this->storesProductRelationFactory->create();
        $storeProductRelation->saveAssignedStores($selectedData, $productId);
    }

    /**
     * get all pickup categories
     * @param int $pickupStoreCatId
     * @return array
     */
    private function getAllPickupCategories($pickupStoreCatId)
    {
        $collection = $this->categoryFactory->create()->getCollection()
            ->addFieldToFilter('parent_id', ['eq' => $pickupStoreCatId]);

        $allCategoryIds = [0];
        if ($collection->getSize()) {
            $allCategoryIds = array_column($collection->getData(), 'entity_id');
        }

        return $allCategoryIds;
    }

    /**
     * get pickup stores category id
     * @param void
     * @return int
     */
    private function getPickupStoresCategoryId()
    {
        $collection = $this->categoryFactory->create()->getCollection()
            ->addFieldToFilter('url_key', ['eq' => 'pickup-stores']);

        foreach ($collection as $category) {
            return $category->getId();
        }

        return false;
    }

    /**
     * get categories from pickup store
     * @param array $params
     * @param int $pickupStoreCatId
     * @return array
     */
    private function getCategoriesFromPickupStore($params, $pickupStoreCatId)
    {
        $storeIds = [];
        $catagoryIds = [0];
        if (!empty($params['links']['storepickup'])) {
            $storeIds = array_column($params['links']['storepickup'], 'id');

            $urlKeys = [];
            foreach ($storeIds as $storeId) {
                $urlKeys[] = 'pickup-store-'.$storeId;
            }

            if (!empty($urlKeys)) {
                $collection = $this->categoryFactory->create()->getCollection()
                    ->addFieldToFilter('url_key', ['in' => $urlKeys])
                    ->addFieldToFilter('parent_id', ['eq' => $pickupStoreCatId]);

                if ($collection->getSize()) {
                    $catagoryIds = array_column($collection->getData(), 'entity_id');
                }
            }
        }

        return $catagoryIds;
    }

    /**
     * get Categories To Be Delete
     * @param array $allPickupCategories
     * @param array $pickupCategoryIds
     * @return array
     */
    private function getCategoriesToBeDelete($allPickupCategories, $pickupCategoryIds)
    {
        $temp = [];
        foreach ($allPickupCategories as $allCategoryId) {
            if (!in_array($allCategoryId, $pickupCategoryIds)) {
                $temp[] = $allCategoryId;
            }
        }

        return $temp;
    }

    /**
     * set product categories
     * @param array $productCategories
     * @param array $categories
     * @param object $product
     * @param array $pickupCategoryIds
     */
    private function setProductCategories($productCategories, $categories, $product, $pickupCategoryIds)
    {
        $temp = [];

        foreach ($productCategories as $productCategory) {
            if (!in_array($productCategory, $categories)) {
                $temp[] = $productCategory;
            }
        }

        if ($pickupCategoryIds[0] > 0) {
            $temp = array_merge($temp, $pickupCategoryIds);
        }

        $product->setCategoryIds($temp);
    }

    /**
     * save pickup categories
     * @param array $allCategoryIds
     * @param array $categoryIds
     * @param int $productId
     * @return void
     */
    private function savePickupCategories($allCategoryIds, $categoryIds, $productId)
    {
        $storeProductRelation = $this->storesProductRelationFactory->create();
        $storeProductRelation->saveAssignedCategoriesToProduct(
            $allCategoryIds,
            $categoryIds,
            $productId
        );
    }
}
