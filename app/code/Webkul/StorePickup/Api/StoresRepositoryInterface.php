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

interface StoresRepositoryInterface
{

    /**
     * Save Stores
     * @param \Webkul\StorePickup\Api\Data\StoresInterface $stores
     * @return \Webkul\StorePickup\Api\Data\StoresInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function save(
        \Webkul\StorePickup\Api\Data\StoresInterface $stores
    );

    /**
     * Retrieve Stores
     * @param string $entityId
     * @return \Webkul\StorePickup\Api\Data\StoresInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function get($entityId);

    /**
     * Retrieve Stores matching the specified criteria.
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @return \Webkul\StorePickup\Api\Data\StoresSearchResultsInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getList(
        \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
    );

    /**
     * Delete Stores
     * @param \Webkul\StorePickup\Api\Data\StoresInterface $stores
     * @return bool true on success
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function delete(
        \Webkul\StorePickup\Api\Data\StoresInterface $stores
    );

    /**
     * Delete Stores by ID
     * @param string $entityId
     * @return bool true on success
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function deleteById($entityId);
}
