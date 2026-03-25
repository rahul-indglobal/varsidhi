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



namespace Lof\Rma\Controller\Adminhtml\Field;

use Magento\Framework\Controller\ResultFactory;

class Edit extends \Lof\Rma\Controller\Adminhtml\Field
{
    /**
     * {@inheritdoc}
     */
    public function execute()
    {
        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        $resultPage = $this->resultFactory->create(ResultFactory::TYPE_PAGE);

        $field = $this->_initField();

        if ($field->getId()) {
            $this->initPage($resultPage);
            $resultPage->getConfig()->getTitle()->prepend(__("Edit Field '%1'", $field->getName()));
            $this->_addBreadcrumb(
                __('Custom Fields'),
                __('Custom Fields'),
                $this->getUrl('*/*/')
            );
            $this->_addBreadcrumb(
                __('Edit Field '),
                __('Edit Field ')
            );

            $resultPage->getLayout()
                ->getBlock('head')
                ;
            $this->_addContent($resultPage->getLayout()->createBlock('\Lof\Rma\Block\Adminhtml\Field\Edit'));

            return $resultPage;
        } else {
            $this->messageManager->addError(__('The Field does not exist.'));
            /** @var \Magento\Framework\Controller\Result\Redirect $resultRedirect */
            $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);

            return $resultRedirect->setPath('*/*/');
        }
    }
}
