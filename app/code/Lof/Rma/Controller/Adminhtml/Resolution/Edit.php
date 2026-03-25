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



namespace Lof\Rma\Controller\Adminhtml\Resolution;

use Magento\Framework\Controller\ResultFactory;

class Edit extends \Lof\Rma\Controller\Adminhtml\Resolution
{
    /**
     * {@inheritdoc}
     */
    public function execute()
    {
        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        $resultPage = $this->resultFactory->create(ResultFactory::TYPE_PAGE);

        $resolution = $this->_initResolution();

        if ($resolution->getId()) {
            $this->initPage($resultPage);
            $resultPage->getConfig()->getTitle()->prepend(__("Edit Resolution '%1'", $resolution->getName()));
            $this->_addBreadcrumb(
                __('Resolution'),
                __('Resolution'),
                $this->getUrl('*/*/')
            );
            $this->_addBreadcrumb(
                __('Edit Resolution '),
                __('Edit Resolution ')
            );

            $resultPage->getLayout()
                ->getBlock('head')
                ;
            $this->_addContent($resultPage->getLayout()->createBlock('\Lof\Rma\Block\Adminhtml\Resolution\Edit'));

            return $resultPage;
        } else {
            /** @var \Magento\Framework\Controller\Result\Redirect $resultRedirect */
            $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
            $this->messageManager->addError(__('The Resolution does not exist.'));
            return $resultRedirect->setPath('*/*/');
        }
    }
}
