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

class OrderSaveAfter implements ObserverInterface
{
    public function __construct(
        \Lof\Rma\Model\OrderStatusHistoryFactory $orderStatusHistoryFactory,
        \Lof\Rma\Model\OrderStatusHistoryRepository $orderStatusHistoryRepository
    ) {
        $this->orderStatusHistoryFactory    = $orderStatusHistoryFactory;
        $this->orderStatusHistoryRepository = $orderStatusHistoryRepository;
    }

    /**
     * {@inheritdoc}
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        /** @var \Magento\Sales\Model\Order $order */
        if (!$order = $observer->getEvent()->getOrder()) {
            return;
        }
        $status = $order->getStatus();
        $historyStatus = $this->orderStatusHistoryFactory->create()->getCollection()
                ->addFieldToFilter('order_id', $order->getId())
                ->getFirstItem();

        if ($status != $historyStatus->getStatus()) {
            $historyStatus->setOrderId($order->getId());
            $historyStatus->setStatus($status);
            $historyStatus->setCreatedAt(strtotime($order->getUpdatedAt()));
            $this->orderStatusHistoryRepository->save($historyStatus);
        }
    }
}
