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

namespace Webkul\StorePickup\Model\ResourceModel\StoresProductsRelation;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    /**
     * @var $_idFieldName
     */
    protected $_idFieldName = 'entity_id';

    /**
     * @var $_eventPrefix
     */
    protected $_eventPrefix = 'webkul_storepickup_stores_products_relation_collection';

    /**
     * @var $_eventObject
     */
    protected $_eventObject = 'webkul_storepickup_stores_products_relation_collection';

    /**
     * Define resource model
     * @return void
     */
    protected function _construct()
    {
        $this->_init(
            \Webkul\StorePickup\Model\StoresProductsRelation::class,
            \Webkul\StorePickup\Model\ResourceModel\StoresProductsRelation::class
        );
    }
}
