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



namespace Lof\Rma\Controller\Adminhtml\Rma;

use Magento\Framework\Controller\ResultFactory;
use Lof\Rma\Controller\Adminhtml\Rma;

class Add extends Rma
{
    public function __construct(
        \Lof\Rma\Model\RmaFactory $rmaFactory,
        \Magento\Framework\Registry $registry,
        \Magento\Backend\App\Action\Context $context
    ) {
        $this->rmaFactory = $rmaFactory;
        $this->registry = $registry;
        parent::__construct($context);
    }

    /**
     * {@inheritdoc}
     */
    public function execute()
    {
        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        $resultPage = $this->resultFactory->create(ResultFactory::TYPE_PAGE);

        $this->initPage($resultPage)->getConfig()->getTitle()->prepend(__('New RMA'));

        $data = $this->backendSession->getFormData(true);
      

        $rma = $this->rmaFactory->create();
        if (!empty($data)) {
            $rma->setData($data);
        }

        $this->registry->register('current_rma', $rma);
        if ($orderId  = $this->getRequest()->getParam('order_id')) {
            
            $this->_addContent($resultPage->getLayout()->createBlock('\Lof\Rma\Block\Adminhtml\Rma\Edit'))->_addLeft($resultPage->getLayout()->createBlock('\Lof\Rma\Block\Adminhtml\Rma\Edit\Tabs'));
        } 

        return $resultPage;
    }
}
