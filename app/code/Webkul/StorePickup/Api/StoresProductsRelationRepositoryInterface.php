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

namespace Webkul\StorePickup\Api;

use Magento\Framework\Api\SearchCriteriaInterface;

interface StoresProductsRelationRepositoryInterface
{

    /**
     * Save StoresProductsRelation
     * @param \Webkul\StorePickup\Api\Data\StoresProductsRelationInterface $storesProductsRelation
     * @return \Webkul\StorePickup\Api\Data\StoresProductsRelationInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function save(
        \Webkul\StorePickup\Api\Data\StoresProductsRelationInterface $storesProductsRelation
    );

    /**
     * Retrieve StoresProductsRelation
     * @param string $entityId
     * @return \Webkul\StorePickup\Api\Data\StoresProductsRelationInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function get($entityId);

    /**
     * Retrieve StoresProductsRelation matching the specified criteria.
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @return \Webkul\StorePickup\Api\Data\StoresProductsRelationSearchResultsInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getList(
        \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
    );

    /**
     * Delete StoresProductsRelation
     * @param \Webkul\StorePickup\Api\Data\StoresProductsRelationInterface $storesProductsRelation
     * @return bool true on success
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function delete(
        \Webkul\StorePickup\Api\Data\StoresProductsRelationInterface $storesProductsRelation
    );

    /**
     * Delete StoresProductsRelation by ID
     * @param string $storesproductsrelationId
     * @return bool true on success
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function deleteById($storesproductsrelationId);
}
