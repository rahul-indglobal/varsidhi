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

namespace Webkul\StorePickup\Model\ResourceModel\StoresHolidays;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    /**
     * @var $_idFieldName
     */
    protected $_idFieldName = 'entity_id';

    /**
     * @var $_eventPrefix
     */
    protected $_eventPrefix = 'webkul_storepickup_holidays_collection';

    /**
     * @var $_eventObject
     */
    protected $_eventObject = 'webkul_storepickup_holidays_collection';

    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(
            \Webkul\StorePickup\Model\StoresHolidays::class,
            \Webkul\StorePickup\Model\ResourceModel\StoresHolidays::class
        );
    }
}
