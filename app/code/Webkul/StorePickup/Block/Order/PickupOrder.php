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

namespace Webkul\StorePickup\Block\Order;

class PickupOrder extends \Magento\Framework\View\Element\Template
{
    /**
     * @var \Magento\Framework\Registry
     */
    protected $registry;

    /**
     * @var \Webkul\StorePickup\Helper\Data
     */
    protected $helper;

    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Webkul\StorePickup\Helper\Data $helper
    ) {
        $this->registry = $registry;
        $this->helper = $helper;
        parent::__construct($context);
    }

    /**
     * Retrieve current order model instance
     * @return \Magento\Sales\Model\Order
     */
    public function getOrder()
    {
        return $this->registry->registry('current_order');
    }

    /**
     * get pickup store
     * @param int $pickupStoreId
     * @return object
     */
    public function getPickupStore($pickupStoreId)
    {
        return $this->helper->getPickupStore($pickupStoreId);
    }

    /**
     * get address from pickup store
     * @param object $pickupStoreId
     * @return string
     */
    public function getAddressFromPickupStore($pickupStoreId)
    {
        return $this->helper->getAddressFromPickupStore($pickupStoreId);
    }

    /**
     * get contact of pickup store
     * @param int $pickupStoreId
     * @return array
     */
    public function getContactsOfPickupStore($pickupStoreId)
    {
        return $this->helper->getContactsOfPickupStore($pickupStoreId);
    }
}
