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

interface StoresSearchResultsInterface extends \Magento\Framework\Api\SearchResultsInterface
{

    /**
     * Get Stores list.
     * @return \Webkul\StorePickup\Api\Data\StoresInterface[]
     */
    public function getItems();

    /**
     * Set entity_id list.
     * @param \Webkul\StorePickup\Api\Data\StoresInterface[] $items
     * @return $this
     */
    public function setItems(array $items);
}
