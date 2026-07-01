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

class RmaPostdispatch implements ObserverInterface
{
    public function __construct(
        \Magento\Framework\ObjectManagerInterface $objectManager
    ) {
        $this->objectManager = $objectManager;
    }

    /**
     * {@inheritdoc}
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        /** @var \Magento\Framework\App\RequestInterface $request */
        $request = $observer->getRequest();
        /** @var \Magento\Backend\Model\Session\Quote $session */
        $session = $this->objectManager->get('Magento\Backend\Model\Session\Quote');
        if ($request->getFullActionName() == 'sales_order_create_start' && (int)$request->getParam('rma_id')) {
            $session->setRmaId($request->getParam('rma_id'));
        } else {
            $session->unsetRmaId($request->getParam('rma_id'));
        }
    }
}
