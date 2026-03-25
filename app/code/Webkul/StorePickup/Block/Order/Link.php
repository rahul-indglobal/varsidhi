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

/**
 * Sales order link
 */
class Link extends \Magento\Framework\View\Element\Html\Link\Current
{
    /**
     * @var \Magento\Framework\Registry
     */
    protected $registry;

    /**
     * @var \Webkul\StorePickup\Helper\Data
     */
    protected $dataHelper;

    /**
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Framework\App\DefaultPathInterface $defaultPath
     * @param \Magento\Framework\Registry $registry
     * @param \Webkul\AdvancedBookingQrcode\Helper\Data $helper
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Framework\App\DefaultPathInterface $defaultPath,
        \Magento\Framework\Registry $registry,
        \Webkul\StorePickup\Helper\Data $dataHelper,
        array $data = []
    ) {
        $this->registry = $registry;
        $this->dataHelper = $dataHelper;
        parent::__construct(
            $context,
            $defaultPath,
            $data
        );
    }

    /**
     * Retrieve current order model instance
     * @return \Magento\Sales\Model\Order
     */
    private function getOrder()
    {
        return $this->registry->registry('current_order');
    }

    /**
     * @inheritdoc
     * @return string
     */
    public function getHref()
    {
        return $this->getUrl($this->getPath(), ['order_id' => $this->getOrder()->getId()]);
    }

    /**
     * @inheritdoc
     * @return string
     */
    protected function _toHtml()
    {
        if ($this->dataHelper->isModuleEnabled()) {
            $orderId = $this->getOrder()->getId();

            if ($this->dataHelper->isPickupStoreOrder($orderId)) {
                return parent::_toHtml();
            }
        }

        return '';
    }
}
