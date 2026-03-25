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

interface StoresHolidaysRepositoryInterface
{
    /**
     * Save StoresHolidays
     * @param \Webkul\StorePickup\Api\Data\StoresHolidaysInterface $storesHolidays
     * @return \Webkul\StorePickup\Api\Data\StoresHolidaysInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function save(
        \Webkul\StorePickup\Api\Data\StoresHolidaysInterface $storesHolidays
    );

    /**
     * Retrieve StoresHolidays
     * @param string $entityId
     * @return \Webkul\StorePickup\Api\Data\StoresHolidaysInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function get($entityId);

    /**
     * Retrieve StoresHolidays matching the specified criteria.
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @return \Webkul\StorePickup\Api\Data\StoresHolidaysSearchResultsInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getList(
        \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
    );

    /**
     * Delete StoresHolidays
     * @param \Webkul\StorePickup\Api\Data\StoresHolidaysInterface $storesHolidays
     * @return bool true on success
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function delete(
        \Webkul\StorePickup\Api\Data\StoresHolidaysInterface $storesHolidays
    );

    /**
     * Delete StoresHolidays by ID
     * @param string $entityId
     * @return bool true on success
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function deleteById($entityId);
}
