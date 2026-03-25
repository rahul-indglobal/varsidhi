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

namespace Webkul\StorePickup\Model\ResourceModel;

class StoresProductsRelation extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    /**
     * Define resource model
     * @return void
     */
    protected function _construct()
    {
        $this->_init('webkul_storepickup_stores_products_relation', 'entity_id');
    }

    /**
     * delete unassigned stores
     * @param object $connection
     * @param array $storeIds
     * @param int $productId
     * @return void
     */
    private function deleteUnassignedStores($connection, $storeIds, $productId)
    {
        if (empty($storeIds)) {
            $storeIds = [0];
        }

        try {
            $connection->beginTransaction();
            $connection->delete(
                $this->getTable('webkul_storepickup_stores_products_relation'),
                [
                    'product_id = ?' => $productId,
                    'store_id not in (?)' => $storeIds
                ]
            );
            $connection->commit();
        } catch (\Exception $e) {
            $connection->rollBack();
        }
    }

    /**
     * update assigned stores
     * @param object $connection
     * @param array $selectedData
     * @return void
     */
    private function updateAssignedStores($connection, $selectedData)
    {
        $qtyConditions = [];
        $storeIdsConditions = [];
        foreach ($selectedData as $store) {
            $case = $connection->quoteInto('?', $store['entity_id']);
            $result = $connection->quoteInto('?', $store['qty']);
            $qtyConditions[$case] = $result;
            $result = $connection->quoteInto('?', $store['store_id']);
            $storeIdsConditions[$case] = $result;
        }

        $qty = $connection->getCaseSql('entity_id', $qtyConditions, 'qty');
        $storeId = $connection->getCaseSql('entity_id', $storeIdsConditions, 'store_id');
        $where = ['entity_id IN (?)' => array_column($selectedData, 'entity_id')];

        try {
            $connection->beginTransaction();
            $connection->update(
                $this->getTable('webkul_storepickup_stores_products_relation'),
                [
                    'qty' => $qty,
                    'store_id' => $storeId
                ],
                $where
            );
            $connection->commit();
        } catch (\Exception $e) {
            $connection->rollBack();
        }
    }

    /**
     * insert assigned stores
     * @param object $connection
     * @param array $selectedData
     * @param array $assignedStoresInDB
     * @return void
     */
    private function insertAssignedStores($connection, $selectedData, $assignedStoresInDB)
    {
        $storeIdsInDB = [];
        if ($assignedStoresInDB) {
            $storeIdsInDB = array_unique(array_column($assignedStoresInDB, 'store_id'));
        }

        $temp = [];
        foreach ($selectedData as $store) {
            if (!in_array($store['store_id'], $storeIdsInDB)) {
                $temp[] = $store;
            }
        }

        if (!empty($temp)) {
            $connection->beginTransaction();
            $tableName = $this->getTable('webkul_storepickup_stores_products_relation');
            try {
                $connection->insertMultiple($tableName, $temp);
                $connection->commit();
            } catch (\Exception $ex) {
                $connection->rollBack();
            }
        }
    }

    /**
     * get assigned stores
     * @param object $connection
     * @param int $productId
     * @return void
     */
    private function getAssignedStores($connection, $productId)
    {
        $select = $connection->select()
            ->from(['relation' => $this->getTable('webkul_storepickup_stores_products_relation')], ['*'])
            ->where('product_id = ?', $productId);

        $connection->beginTransaction();
        try {
            $result = $connection->fetchAll($select);
            $connection->commit();
            return $result;
        } catch (\Exception $e) {
            $connection->rollBack();
        }
    }

    /**
     * Save Assigned Stores
     * @param object \Webkul\StorePickup\Model\StoresProductsRelation
     * @param array $selectedData
     * @param int $productId
     * @return void
     */
    public function saveAssignedStores($storePickupRelation, $selectedData, $productId)
    {
        if ($storePickupRelation instanceof \Webkul\StorePickup\Model\StoresProductsRelation) {
            $storeIds = array_unique(array_column($selectedData, 'store_id'));
            $connection = $this->getConnection();
            $this->deleteUnassignedStores($connection, $storeIds, $productId);
            $assignedStoresInDB = $this->getAssignedStores($connection, $productId);

            if ($assignedStoresInDB && !empty($selectedData)) {
                $temp = [];
                foreach ($assignedStoresInDB as $dbStore) {
                    $temp[] = [
                        'entity_id' => $dbStore['entity_id'],
                        'store_id' => $selectedData[$dbStore['store_id']]['store_id'],
                        'product_id' => $productId,
                        'qty' => $selectedData[$dbStore['store_id']]['qty']
                    ];
                }

                $this->updateAssignedStores($connection, $temp);
            }

            $this->insertAssignedStores($connection, $selectedData, $assignedStoresInDB);
        }
    }

    /**
     * delete unassigned products
     * @param object $connection
     * @param array $productIds
     * @param int $storeId
     * @return void
     */
    private function deleteUnassignedProducts($connection, $productIds, $storeId)
    {
        if (empty($productIds)) {
            $productIds = [0];
        }

        try {
            $connection->beginTransaction();
            $connection->delete(
                $this->getTable('webkul_storepickup_stores_products_relation'),
                [
                    'store_id = ?' => $storeId,
                    'product_id not in (?)' => $productIds
                ]
            );
            $connection->commit();
        } catch (\Exception $e) {
            $connection->rollBack();
        }
    }

    /**
     * get assigned products
     * @param object $connection
     * @param int $storeId
     * @return array
     */
    private function getAssignedProducts($connection, $storeId)
    {
        $select = $connection->select()
            ->from(['relation' => $this->getTable('webkul_storepickup_stores_products_relation')], ['*'])
            ->where('store_id = ?', $storeId);

        $connection->beginTransaction();
        try {
            $result = $connection->fetchAll($select);
            $connection->commit();
            return $result;
        } catch (\Exception $e) {
            $connection->rollBack();
        }
    }

    /**
     * Update Assigned Products
     * @param object $connection
     * @param array $selectedData
     * @return void
     */
    private function updateAssignedProducts($connection, $selectedData)
    {
        $qtyConditions = [];
        $productIdsConditions = [];
        foreach ($selectedData as $store) {
            $case = $connection->quoteInto('?', $store['entity_id']);
            $result = $connection->quoteInto('?', $store['qty']);
            $qtyConditions[$case] = $result;
            $result = $connection->quoteInto('?', $store['product_id']);
            $productIdsConditions[$case] = $result;
        }

        $qty = $connection->getCaseSql('entity_id', $qtyConditions, 'qty');
        $productId = $connection->getCaseSql('entity_id', $productIdsConditions, 'product_id');
        $where = ['entity_id IN (?)' => array_column($selectedData, 'entity_id')];

        try {
            $connection->beginTransaction();
            $connection->update(
                $this->getTable('webkul_storepickup_stores_products_relation'),
                [
                    'qty' => $qty,
                    'product_id' => $productId
                ],
                $where
            );
            $connection->commit();
        } catch (\Exception $e) {
            $connection->rollBack();
        }
    }

    /**
     * Insert Assigned Products
     * @param object $connection
     * @param array $selectedData
     * @param array $assignedStoresInDB
     * @return void
     */
    private function insertAssignedProducts($connection, $selectedData, $assignedStoresInDB)
    {
        $productIdsInDB = [];
        if ($assignedStoresInDB) {
            $productIdsInDB = array_unique(array_column($assignedStoresInDB, 'product_id'));
        }

        $temp = [];
        foreach ($selectedData as $product) {
            if (!in_array($product['product_id'], $productIdsInDB)) {
                $temp[] = $product;
            }
        }

        if (!empty($temp)) {
            $connection->beginTransaction();
            $tableName = $this->getTable('webkul_storepickup_stores_products_relation');
            try {
                $connection->insertMultiple($tableName, $temp);
                $connection->commit();
            } catch (\Exception $ex) {
                $connection->rollBack();
            }
        }
    }

    /**
     * Save Assigned Products
     * @param object \Webkul\StorePickup\Model\StoresProductsRelation
     * @param array $selectedData
     * @param int $storeId
     * @return void
     */
    public function saveAssignedProducts($storePickupRelation, $selectedData, $storeId)
    {
        if ($storePickupRelation instanceof \Webkul\StorePickup\Model\StoresProductsRelation) {
            $productIds = array_unique(array_column($selectedData, 'product_id'));
            $connection = $this->getConnection();
            $this->deleteUnassignedProducts($connection, $productIds, $storeId);

            $assignedProductsInDB = $this->getAssignedProducts($connection, $storeId);

            if ($assignedProductsInDB && !empty($selectedData)) {
                $temp = [];
                foreach ($assignedProductsInDB as $dbProduct) {
                    $temp[] = [
                        'entity_id' => $dbProduct['entity_id'],
                        'store_id' => $storeId,
                        'product_id' => $selectedData[$dbProduct['product_id']]['product_id'],
                        'qty' => $selectedData[$dbProduct['product_id']]['qty']
                    ];
                }

                $this->updateAssignedProducts($connection, $temp);
            }

            $this->insertAssignedProducts($connection, $selectedData, $assignedProductsInDB);
        }
    }

    /**
     * delete unassigned products from category
     * @param object $connection
     * @param array $productIds
     * @param int $categoryId
     * @return void
     */
    private function deleteUnassignedProductsFromCategory($connection, $productIds, $categoryId)
    {
        if (empty($productIds)) {
            $productIds = [0];
        }

        try {
            $connection->beginTransaction();
            $connection->delete(
                $this->getTable('catalog_category_product'),
                [
                    'category_id = ?' => $categoryId,
                    'product_id not in (?)' => $productIds
                ]
            );
            $connection->commit();
        } catch (\Exception $e) {
            $connection->rollBack();
        }
    }

    /**
     * get Assigned Products In Category
     * @param object $connection
     * @param int $categoryId
     * @return array
     */
    private function getAssignedProductsInCategory($connection, $categoryId)
    {
        $select = $connection->select()
            ->from(['relation' => $this->getTable('catalog_category_product')], ['*'])
            ->where('category_id = ?', $categoryId);

        $connection->beginTransaction();
        try {
            $result = $connection->fetchAll($select);
            $connection->commit();
            return $result;
        } catch (\Exception $e) {
            $connection->rollBack();
        }
    }

    /**
     * insert Assigned Products In Category
     * @param object $connection
     * @param array $productIds
     * @param int $categoryId
     * @param array $assignedProductsInDB
     * @return void
     */
    private function insertAssignedProductsInCategory($connection, $productIds, $categoryId, $assignedProductsInDB)
    {
        $productIdsInDB = [];
        if ($assignedProductsInDB) {
            $productIdsInDB = array_unique(array_column($assignedProductsInDB, 'product_id'));
        }

        $temp = [];
        foreach ($productIds as $productId) {
            if (!in_array($productId, $productIdsInDB)) {
                $temp[] = [
                    'category_id' => $categoryId,
                    'product_id' => $productId,
                    'position' => 0
                ];
            }
        }

        if (!empty($temp)) {
            $connection->beginTransaction();
            $tableName = $this->getTable('catalog_category_product');
            try {
                $connection->insertMultiple($tableName, $temp);
                $connection->commit();
            } catch (\Exception $ex) {
                $connection->rollBack();
            }
        }
    }

    /**
     * Save Assigned Products to category
     * @param object $storePickupRelation
     * @param array $productIds
     * @param int $categoryId
     * @return void
     */
    public function saveAssignedProductsToCategory($storePickupRelation, $productIds, $categoryId)
    {
        if ($storePickupRelation instanceof \Webkul\StorePickup\Model\StoresProductsRelation) {
            $connection = $this->getConnection();
            $this->deleteUnassignedProductsFromCategory($connection, $productIds, $categoryId);
            $assignedProductsInDB = $this->getAssignedProductsInCategory($connection, $categoryId);
            $this->insertAssignedProductsInCategory($connection, $productIds, $categoryId, $assignedProductsInDB);
        }
    }

    /**
     * delete Unassigned Categories From Product
     * @param object $connection
     * @param array $allCategoryIds
     * @param array $categoryIds
     * @param int $productId
     * @return void
     */
    private function deleteUnassignedCategoriesFromProduct($connection, $allCategoryIds, $categoryIds, $productId)
    {
        $temp = [];
        foreach ($allCategoryIds as $allCategoryId) {
            if (!in_array($allCategoryId, $categoryIds)) {
                $temp[] = $allCategoryId;
            }
        }

        try {
            $connection->beginTransaction();
            $connection->delete(
                $this->getTable('catalog_category_product'),
                [
                    'product_id = ?' => $productId,
                    'category_id in (?)' => $temp
                ]
            );

            $connection->commit();
        } catch (\Exception $e) {
            $connection->rollBack();
        }
    }

    /**
     * get Assigned Categories In Product
     * @param object $connection
     * @param array $allCategoryIds
     * @param int $productId
     * @return void
     */
    private function getAssignedCategoriesInProduct($connection, $allCategoryIds, $productId)
    {
        $select = $connection->select()
            ->from(['relation' => $this->getTable('catalog_category_product')], ['*'])
            ->where('product_id = ?', $productId)
            ->where('category_id in (?)', $allCategoryIds);

        $connection->beginTransaction();
        try {
            $result = $connection->fetchAll($select);
            $connection->commit();
            return $result;
        } catch (\Exception $e) {
            $connection->rollBack();
        }
    }

    /**
     * insert Assigned Categories In Product
     * @param object $connection
     * @param array $categoryIds
     * @param int $productId
     * @param array $assignedCategoriesInDB
     * @return void
     */
    private function insertAssignedCategoriesInProduct($connection, $categoryIds, $productId, $assignedCategoriesInDB)
    {
        $assignedCategoriesInDB = array_unique(array_column($assignedCategoriesInDB, 'category_id'));
        $temp = [];

        foreach ($categoryIds as $categoryId) {
            if (!in_array($categoryId, $assignedCategoriesInDB) && $categoryId > 0) {
                $temp[] = [
                    'category_id' => $categoryId,
                    'product_id' => $productId,
                    'position' => 0
                ];
            }
        }

        if (!empty($temp)) {
            $connection->beginTransaction();
            $tableName = $this->getTable('catalog_category_product');
            try {
                $connection->insertMultiple($tableName, $temp);
                $connection->commit();
            } catch (\Exception $ex) {
                $connection->rollBack();
            }
        }
    }

    /**
     * Save Assigned Categories to product
     * @param object \Webkul\StorePickup\Model\StoresProductsRelation
     * @param array $allCategoryIds
     * @param array $categoryIds
     * @param int $productId
     * @return void
     */
    public function saveAssignedCategoriesToProduct($storePickupRelation, $allCategoryIds, $categoryIds, $productId)
    {
        if ($storePickupRelation instanceof \Webkul\StorePickup\Model\StoresProductsRelation) {
            $connection = $this->getConnection();
            $this->deleteUnassignedCategoriesFromProduct($connection, $allCategoryIds, $categoryIds, $productId);
            $assignedCategoriesInDB = $this->getAssignedCategoriesInProduct($connection, $allCategoryIds, $productId);
            $this->insertAssignedCategoriesInProduct($connection, $categoryIds, $productId, $assignedCategoriesInDB);
        }
    }
}
