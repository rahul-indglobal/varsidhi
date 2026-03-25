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



namespace Lof\Rma\Observer;

use Magento\Framework\Event\ObserverInterface;

class QuoteSubmitAfter implements ObserverInterface
{
    public function __construct(
        \Magento\Backend\Model\Session\Quote $quoteSession,
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Magento\Framework\App\ResourceConnection $resource,
        \Lof\Rma\Model\RmaFactory $rmaFactory
    ) {
        $this->quoteSession  = $quoteSession;
        $this->objectManager = $objectManager;
         $this->_resource      = $resource;
        $this->rmaFactory    = $rmaFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $order = $observer->getOrder();
        if ($rmaId = $this->quoteSession->getRmaId()) {
            /** @var \Lof\Rma\Model\Rma\Interceptor $rma */
            $rma = $this->rmaFactory->create()->load($rmaId);
            $id = $order->getId();
          
            $objArray = [
                're_rma_id' => $rmaId,
                're_exchange_order_id' => $id,
            ];
            $this->_resource->getConnection()->insert(
                 $this->_resource->getTableName('lof_rma_rma_order'),
                $objArray
            );
            $this->quoteSession->unsetRmaId();
        }
    }
}
