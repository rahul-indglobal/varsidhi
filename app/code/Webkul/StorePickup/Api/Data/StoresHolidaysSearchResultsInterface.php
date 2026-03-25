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

interface StoresHolidaysSearchResultsInterface extends \Magento\Framework\Api\SearchResultsInterface
{
    /**
     * Get StoresHolidays list.
     * @return \Webkul\StorePickup\Api\Data\StoresHolidaysInterface[]
     */
    public function getItems();

    /**
     * Set status list.
     * @param \Webkul\StorePickup\Api\Data\StoresHolidaysInterface[] $items
     * @return $this
     */
    public function setItems(array $items);
}
