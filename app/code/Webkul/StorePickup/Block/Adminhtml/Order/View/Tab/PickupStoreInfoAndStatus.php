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

namespace Webkul\StorePickup\Block\Adminhtml\Order\View\Tab;

class PickupStoreInfoAndStatus extends \Magento\Backend\Block\Template implements
    \Magento\Backend\Block\Widget\Tab\TabInterface
{
    /**
     * @var Template
     */
    protected $_template = 'order/view/tab/pickupstoreinfoandstatus.phtml';

    /**
     * @var \Webkul\StorePickup\Helper\Data
     */
    protected $helper;

    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Webkul\StorePickup\Helper\Data $helper
    ) {
        $this->helper = $helper;
        parent::__construct($context);
    }

/************************ Abstract Method Implementation Start ********************/
    /**
     * {@inheritdoc}
     */
    public function getTabLabel()
    {
        return __('Pickup Store Info and Status');
    }

    /**
     * {@inheritdoc}
     */
    public function getTabTitle()
    {
        return __('Pickup Store Info and Status');
    }

    /**
     * {@inheritdoc}
     */
    public function canShowTab()
    {
        if ($this->helper->isModuleEnabled()) {
            $orderId = $this->getCurrentOrderId();
            return $this->helper->isPickupStoreOrder($orderId);
        }

        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function isHidden()
    {
        return false;
    }

/************************ Abstract Method Implementation END ********************/

    /**
     * {@inheritdoc}
     */
    public function getTabClass()
    {
        return 'ajax only';
    }

    /**
     * {@inheritdoc}
     */
    public function getClass()
    {
        return $this->getTabClass();
    }

    /**
     * {@inheritdoc}
     */
    public function getTabUrl()
    {
        return $this->getUrl('storepickup/orders/pickupstoreinfoandstatus', ['_current' => true]);
    }

    /**
     * get current order id
     * @return int
     */
    public function getCurrentOrderId()
    {
        return (int)$this->getRequest()->getParam('order_id');
    }

    /**
     * get order
     * @return object
     */
    public function getOrder()
    {
        $orderId = $this->getCurrentOrderId();
        return $this->helper->getOrder($orderId);
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
     * get store holidays with name
     * @param object $pickupStore
     * @return array
     */
    public function getStoreHolidaysWithName($pickupStore)
    {
        return $this->helper->getStoreHolidaysWithName($pickupStore);
    }

    /**
     * get store weekly holidays
     * @param object $pickupStore
     * @return array
     */
    public function getStoreWeeklyHolidays($pickupStore)
    {
        return $this->helper->getStoreWeeklyHolidays($pickupStore);
    }

    /**
     * get store timing day wise
     * @param object $pickupStore
     * @return array
     */
    public function getStoreTimingDayWise($pickupStore)
    {
        return $this->helper->getStoreTimingDayWise($pickupStore);
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

    /**
     * json encode
     * @param array $data
     * @return string
     */
    public function encode($data)
    {
        return $this->helper->serialize($data);
    }
}
