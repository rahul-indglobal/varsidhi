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

namespace Webkul\StorePickup\Block\Adminhtml\GeoLocation;

class LatLong extends \Magento\Backend\Block\Template
{
    /**
     * @var Webkul\StorePickup\Helper\Data
     */
    private $dataHelper;

    /**
     * @var Template
     */
    protected $_template = 'geolocation/latlong.phtml';

    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
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
}
