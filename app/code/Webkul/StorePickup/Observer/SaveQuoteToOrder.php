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

namespace Webkul\StorePickup\Observer;

use Magento\Framework\Event\ObserverInterface;

class SaveQuoteToOrder implements ObserverInterface
{
    /**
     * execute
     * @param Magento\Framework\Event\Observer $observer
     * @return object
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $order = $observer->getEvent()->getOrder();
        $quote = $observer->getEvent()->getQuote();
        $order->setPickupStore($quote->getPickupStore());
        return $this;
    }
}
