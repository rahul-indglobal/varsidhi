<?php
/**
 * LandOfCoder
 * 
 * NOTICE OF LICENSE
 * 
 * This source file is subject to the Venustheme.com license that is
 * available through the world-wide-web at this URL:
 * http://www.venustheme.com/license-agreement.html
 * 
 * DISCLAIMER
 * 
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 * 
 * @category   LandOfCoder
 * @package    Lof_Rma
 * @copyright  Copyright (c) 2016 Venustheme (http://www.LandOfCoder.com/)
 * @license    http://www.LandOfCoder.com/LICENSE-1.0.html
 */



namespace Lof\Rma\Model\Config\Source\Order;

class Status implements \Magento\Framework\Option\ArrayInterface
{
    public function __construct(
        \Magento\Sales\Model\Order\ConfigFactory $orderConfigFactory,
        \Magento\Framework\Model\Context $context
    ) {
        $this->orderConfigFactory = $orderConfigFactory;
        $this->context            = $context;
    }

    /**
     * @var array
     */
    protected $options;

    /**
     * @return array
     */
    public function toOptionArray()
    {
        if (!$this->options) {
            $this->options = [];
            $statuses = $this->orderConfigFactory->create()->getStatuses();
            foreach ($statuses as $id => $status) {
                $this->options[] = ['value' => $id, 'label' => $status];
            }
        }

        return $this->options;
    }
}
