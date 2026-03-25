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

interface StoresProductsRelationSearchResultsInterface extends \Magento\Framework\Api\SearchResultsInterface
{

    /**
     * Get StoresProductsRelation list.
     * @return \Webkul\StorePickup\Api\Data\StoresProductsRelationInterface[]
     */
    public function getItems();

    /**
     * Set entity_id list.
     * @param \Webkul\StorePickup\Api\Data\StoresProductsRelationInterface[] $items
     * @return $this
     */
    public function setItems(array $items);
}
