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

use Webkul\StorePickup\Api\Data\StoresProductsRelationInterface;

class StoresProductsRelation extends \Magento\Framework\Model\AbstractExtensibleModel implements
    \Magento\Framework\DataObject\IdentityInterface,
    \Webkul\StorePickup\Api\Data\StoresProductsRelationInterface
{
    /**
     * cache tag of stores
     * @var CACHE_TAG
     */
    const CACHE_TAG = 'webkul_storepickup_stores_products_relation';

    /**
     * @var $_cacheTag
     */
    protected $_cacheTag = 'webkul_storepickup_stores_products_relation';

    /**
     * @var $_eventPrefix
     */
    protected $_eventPrefix = 'webkul_storepickup_stores_products_relation';

    /**
     * Constructor
     */
    protected function _construct()
    {
        $this->_init(\Webkul\StorePickup\Model\ResourceModel\StoresProductsRelation::class);
    }

    /**
     * @inheritdoc
     * @return string
     */
    public function getIdentities()
    {
        return [self::CACHE_TAG . '_' . $this->getId()];
    }

    /**
     * @inheritdoc
     * @return string
     */
    public function getDefaultValues()
    {
        $values = [];
        return $values;
    }

    /**
     * Get entity_id
     * @return string|null
     */
    public function getEntityId()
    {
        return $this->getData(self::ENTITY_ID);
    }

    /**
     * Set entity_id
     * @param string $entityId
     * @return \Webkul\StorePickup\Api\Data\StoresProductsRelationInterface
     */
    public function setEntityId($entityId)
    {
        return $this->setData(self::ENTITY_ID, $entityId);
    }

    /**
     * Retrieve existing extension attributes object or create a new one.
     * @return \Webkul\StorePickup\Api\Data\StoresProductsRelationExtensionInterface|null
     */
    public function getExtensionAttributes()
    {
        return $this->_getExtensionAttributes();
    }

    /**
     * Set an extension attributes object.
     * @param \Webkul\StorePickup\Api\Data\StoresProductsRelationExtensionInterface $extensionAttributes
     * @return $this
     */
    public function setExtensionAttributes(
        \Webkul\StorePickup\Api\Data\StoresProductsRelationExtensionInterface $extensionAttributes
    ) {
        return $this->_setExtensionAttributes($extensionAttributes);
    }

    /**
     * Get store_id
     * @return string|null
     */
    public function getStoreId()
    {
        return $this->getData(self::STORE_ID);
    }

    /**
     * Set store_id
     * @param string $storeId
     * @return \Webkul\StorePickup\Api\Data\StoresProductsRelationInterface
     */
    public function setStoreId($storeId)
    {
        return $this->setData(self::STORE_ID, $storeId);
    }

    /**
     * Get product_id
     * @return string|null
     */
    public function getProductId()
    {
        return $this->getData(self::PRODUCT_ID);
    }

    /**
     * Set product_id
     * @param string $productId
     * @return \Webkul\StorePickup\Api\Data\StoresProductsRelationInterface
     */
    public function setProductId($productId)
    {
        return $this->setData(self::PRODUCT_ID, $productId);
    }

    /**
     * Get qty
     * @return string|null
     */
    public function getQty()
    {
        return $this->getData(self::QTY);
    }

    /**
     * Set qty
     * @param string $qty
     * @return \Webkul\StorePickup\Api\Data\StoresProductsRelationInterface
     */
    public function setQty($qty)
    {
        return $this->setData(self::QTY, $qty);
    }

    /**
     * Save Assigned Stores
     * @param array $data
     * @return void
     */
    public function saveAssignedStores($selectedData, $productId)
    {
        if (!$this->hasAssignedStores()) {
            $files = $this->_getResource()->saveAssignedStores($this, $selectedData, $productId);
        }
    }

    /**
     * Save Assigned Products
     * @param array $data
     * @return void
     */
    public function saveAssignedProducts($selectedData, $storeId)
    {
        if (!$this->hasAssignedProducts()) {
            $files = $this->_getResource()->saveAssignedProducts($this, $selectedData, $storeId);
        }
    }

    /**
     * Save Assigned Products to category
     * @param array $selectedData
     * @param int $storeId
     * @return void
     */
    public function saveAssignedProductsToCategory($productIds, $categoryId)
    {
        if (!$this->hasAssignedProductsToCategory()) {
            $files = $this->_getResource()->saveAssignedProductsToCategory($this, $productIds, $categoryId);
        }
    }

    /**
     * Save Assigned Categories to product
     * @param array $categoryIds
     * @param int $productId
     * @return void
     */
    public function saveAssignedCategoriesToProduct($allCategoryIds, $categoryIds, $productId)
    {
        if (!$this->hasAssignedCategoriesToProduct()) {
            $files = $this->_getResource()->saveAssignedCategoriesToProduct(
                $this,
                $allCategoryIds,
                $categoryIds,
                $productId
            );
        }
    }
}
