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

namespace Webkul\StorePickup\Api\Data;

interface StoresProductsRelationInterface extends \Magento\Framework\Api\ExtensibleDataInterface
{
    /**
     * Table Fields
     */
    const ENTITY_ID = 'entity_id';
    const STORE_ID = 'store_id';
    const PRODUCT_ID = 'product_id';
    const QTY = 'qty';

    /**
     * Get entity_id
     * @return string|null
     */
    public function getEntityId();

    /**
     * Set entity_id
     * @param string $entityId
     * @return \Webkul\StorePickup\Api\Data\StoresProductsRelationInterface
     */
    public function setEntityId($entityId);

    /**
     * Retrieve existing extension attributes object or create a new one.
     * @return \Webkul\StorePickup\Api\Data\StoresProductsRelationExtensionInterface|null
     */
    public function getExtensionAttributes();

    /**
     * Set an extension attributes object.
     * @param \Webkul\StorePickup\Api\Data\StoresProductsRelationExtensionInterface $extensionAttributes
     * @return $this
     */
    public function setExtensionAttributes(
        \Webkul\StorePickup\Api\Data\StoresProductsRelationExtensionInterface $extensionAttributes
    );

    /**
     * Get store_id
     * @return string|null
     */
    public function getStoreId();

    /**
     * Set store_id
     * @param string $storeId
     * @return \Webkul\StorePickup\Api\Data\StoresProductsRelationInterface
     */
    public function setStoreId($storeId);

    /**
     * Get product_id
     * @return string|null
     */
    public function getProductId();

    /**
     * Set product_id
     * @param string $productId
     * @return \Webkul\StorePickup\Api\Data\StoresProductsRelationInterface
     */
    public function setProductId($productId);

    /**
     * Get qty
     * @return string|null
     */
    public function getQty();

    /**
     * Set qty
     * @param string $qty
     * @return \Webkul\StorePickup\Api\Data\StoresProductsRelationInterface
     */
    public function setQty($qty);
}
