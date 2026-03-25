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



namespace Lof\Rma\Controller\Adminhtml\Rule;

use Magento\Framework\Controller\ResultFactory;

class Save extends \Lof\Rma\Controller\Adminhtml\Rule
{
    /**
     * {@inheritdoc}
     */
    public function execute()
    {
        /** @var \Magento\Framework\Controller\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);

        if ($data = $this->getRequest()->getParams()) {
            $rule = $this->_initRule();

            if (!$data['status_id']) {
                $data['status_id'] = null;
            }
            if (!$data['user_id']) {
                $data['user_id'] = null;
            }

            $rule->addData($data);
            if (isset($data['rule'])) {
                $rule->loadPost($data['rule']);
            }

            try {
                $rule->save();

                $this->messageManager->addSuccess(__('Rule was successfully saved'));
                $this->backendSession->setFormData(false);

                if ($this->getRequest()->getParam('back')) {
                    return $resultRedirect->setPath('*/*/edit', ['id' => $rule->getId()]);

                    return;
                }
                return $resultRedirect->setPath('*/*/');

                return;
            } catch (\Magento\Framework\Exception\LocalizedException $e) {
                $this->messageManager->addError($e->getMessage());
                $this->backendSession->setFormData($data);
                return $resultRedirect->setPath('*/*/edit', ['id' => $this->getRequest()->getParam('id')]);

                return;
            }
        }
        $this->messageManager->addError(__('Unable to find Rule to save'));
        return $resultRedirect->setPath('*/*/');
    }
}
