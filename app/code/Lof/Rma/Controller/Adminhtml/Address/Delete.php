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


namespace Lof\Rma\Controller\Adminhtml\Address;

use Magento\Framework\Controller\ResultFactory;

class Delete extends \Lof\Rma\Controller\Adminhtml\Address
{
    /**
     * {@inheritdoc}
     */
    public function execute()
    {
        /** @var \Magento\Framework\Controller\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);

        if ($this->getRequest()->getParam('id') > 0) {
            try {
                $address = $this->addressFactory->create();

                $address->setId($this->getRequest()
                    ->getParam('id'))
                    ->delete();

                $this->messageManager->addSuccess(
                    __('Return Address was successfully deleted')
                );
                return $resultRedirect->setPath('*/*/');
            } catch (\Magento\Framework\Exception\LocalizedException $e) {
                $this->messageManager->addError($e->getMessage());
                return $resultRedirect->setPath('*/*/edit', ['id' => $this->getRequest()
                    ->getParam('id'), ]);
            }
        }
        return $resultRedirect->setPath('*/*/');
    }
}
