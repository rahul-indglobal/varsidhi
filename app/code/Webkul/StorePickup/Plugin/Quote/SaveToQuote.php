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

namespace Webkul\StorePickup\Plugin\Quote;

use Magento\Quote\Model\QuoteRepository;

class SaveToQuote
{
    /**
     * @var $quoteRepository
     */
    protected $quoteRepository;

    /**
     * Constructor
     * @param QuoteRepository $quoteRepository
     */
    public function __construct(QuoteRepository $quoteRepository)
    {
        $this->quoteRepository = $quoteRepository;
    }

    /**
     * before save address information
     * @param Magento\Checkout\Model\ShippingInformationManagement $subject
     * @param int $cartId
     * @param Magento\Checkout\Api\Data\ShippingInformationInterface $addressInformation
     * @return void
     */
    public function beforeSaveAddressInformation(
        \Magento\Checkout\Model\ShippingInformationManagement $subject,
        $cartId,
        \Magento\Checkout\Api\Data\ShippingInformationInterface $addressInformation
    ) {
        if (!$extAttributes = $addressInformation->getExtensionAttributes()) {
            return;
        }

        $quote = $this->quoteRepository->getActive($cartId);
        $pickupStore = $extAttributes->getPickupStore();

        if ($pickupStore) {
            $pickupStore = substr($pickupStore, strrpos($pickupStore, '_')+1);
        }
        $quote->setPickupStore($pickupStore);
    }
}
