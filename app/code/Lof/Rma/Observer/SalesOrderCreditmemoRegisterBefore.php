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

class SalesOrderCreditmemoRegisterBefore implements ObserverInterface
{
    public function __construct(
        \Magento\Framework\App\Request\Http $request,
        \Magento\Backend\Model\Session $backendSession
    ) {
        $this->request        = $request;
        $this->backendSession = $backendSession;
    }

    /**
     * Save rma id to session when create credit memo in the backend.
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     * @return void
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        if ($rmaId = $this->request->getParam('rma_id')) {
            $this->backendSession->setRmaId($rmaId);
        }
    }
}
