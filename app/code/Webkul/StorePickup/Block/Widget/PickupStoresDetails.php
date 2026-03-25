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

namespace Webkul\StorePickup\Block\Widget;

use Magento\Framework\View\Element\Template;
use Magento\Widget\Block\BlockInterface;

class PickupStoresDetails extends Template implements BlockInterface
{
    /**
     * @var $_template
     */
    protected $_template = "widget/pickup_stores_details.phtml";

    /**
     * @var Webkul\StorePickup\Helper\Data
     */
    private $dataHelper;

    /**
     * Constructor
     * @param Magento\Framework\View\Element\Template\Context $context
     * @param Webkul\Store\Pickup\Helper\Data                 $dataHelper
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Webkul\StorePickup\Helper\Data $dataHelper
    ) {
        $this->dataHelper = $dataHelper;
        parent::__construct($context);
    }

    /**
     * get Pickup Store Id
     * @param void
     * @return string
     */
    public function getPickupStoreId()
    {
        $path = $this->getRequest()->getOriginalPathInfo();
        $path = explode("?", $path)[0];
        $path = explode("pickup-store-", $path)[1];
        return explode(".", $path)[0];
    }

    /**
     * get pickup store
     * @param void
     * @return object
     */
    public function getPickupStore()
    {
        $pickupStoreId = $this->getPickupStoreId();
        return $this->dataHelper->getPickupStore($pickupStoreId);
    }

    /**
     * get pickup store address
     * @param void
     * @return string
     */
    public function getPickupStoreAddress()
    {
        $pickupStoreId = $this->getPickupStoreId();
        return $this->dataHelper->getAddressFromPickupStore($pickupStoreId);
    }

    /**
     * get pickup store contact
     * @return array
     */
    public function getPickupStoreContact()
    {
        $pickupStoreId = $this->getPickupStoreId();
        return $this->dataHelper->getContactsOfPickupStore($pickupStoreId);
    }

    /**
     * is store open
     * @return boolean
     */
    public function isStoreOpen()
    {
        $pickupStore = $this->getPickupStore();
        return $this->dataHelper->isStoreOpen($pickupStore);
    }
}
