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

class PickupStores extends Template implements BlockInterface
{
    /**
     * @var $_template
     */
    protected $_template = "widget/pickup_stores.phtml";

    /**
     * @var Webkul\StorePickup\Helper\Data
     */
    private $dataHelper;

    /**
     * Constructor
     * @param Magento\Framework\View\Element\Template\Context $context
     * @param Webkul\StorePickup\Helper\Data                  $dataHelper
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Webkul\StorePickup\Helper\Data $dataHelper
    ) {
        $this->dataHelper = $dataHelper;
        parent::__construct($context);
    }

    /**
     * check server ssl
     * @param void
     * @return boolean
     */
    public function isSecureServer()
    {
        return $this->dataHelper->isSecureServer();
    }

    /**
     * is pickup stores page enabled
     * @param void
     * @return boolean
     */
    public function isPickupStoresPageEnabled()
    {
        return $this->dataHelper->isPickupStoresPageEnabled();
    }

    /**
     * get within range
     * @param void
     * @return int
     */
    public function getWithinRange()
    {
        return $this->dataHelper->getWithinRange();
    }

    /**
     * is pickup stores page's address search enabled
     * @param void
     * @return boolean
     */
    public function isAddressSearchEnabled()
    {
        return $this->dataHelper->isAddressSearchEnabled();
    }

    /**
     * get nearest pickup stores by range
     * @param string $originLat
     * @param string $originLng
     * @param int $range
     * @return array
     */
    public function getNearestPickupStoresByRange($originLat, $originLng, $range)
    {
        return $this->dataHelper->getNearestPickupStoresByRange($originLat, $originLng, $range);
    }

    /**
     * is google map enabled
     * @param void
     * @return boolean
     */
    public function isGoogleMapEnabled()
    {
        return $this->dataHelper->isGoogleMapEnabled();
    }

    /**
     * get google api key
     * @param void
     * @return string|null
     */
    public function getGoogleApiKey()
    {
        return $this->dataHelper->getGoogleApiKey();
    }
}
