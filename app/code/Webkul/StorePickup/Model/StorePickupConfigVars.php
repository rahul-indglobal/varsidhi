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

namespace Webkul\StorePickup\Model;

use \Magento\Checkout\Model\ConfigProviderInterface;

class StorePickupConfigVars implements ConfigProviderInterface
{
    /**
     * @var \Webkul\StorePickup\Helper\Data
     */
    private $dataHelper;

    public function __construct(
        \Webkul\StorePickup\Helper\Data $dataHelper
    ) {
        $this->dataHelper = $dataHelper;
    }

    /**
     * This function is adding the configuration values in window.checkoutConfig
     * @param void
     * @return array
     */
    public function getConfig()
    {
        $active = $this->dataHelper->isModuleEnabled();
        $isGoogleMapEnabled = $this->dataHelper->isGoogleMapEnabled();
        $googleKey = $this->dataHelper->getGoogleApiKey();
        $additionalVariables = [];
        $additionalVariables['isStorePickupEnabled'] = $active;
        $additionalVariables['isGoogleMapEnabled'] = $isGoogleMapEnabled;
        $additionalVariables['googleKey'] = $googleKey;
        return $additionalVariables;
    }
}
