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
use Magento\Framework\Exception\LocalizedException;
use Lof\Rma\Controller\Adminhtml\Rma;

class MarkRead extends Rma
{
    public function __construct(
        \Lof\Rma\Model\RmaFactory $rmaFactory,
        \Magento\Backend\App\Action\Context $context
    ) {
        $this->rmaFactory = $rmaFactory;

        parent::__construct($context);
    }

    /**
     * {@inheritdoc}
     */
    public function execute()
    {
        /** @var \Magento\Framework\Controller\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        $id = (int)$this->getRequest()->getParam('rma_id');
        $rma = $this->rmaFactory->create()->load($id);
        if (!$rma->getId()) {
            return $resultRedirect->setPath('*/*/');
        }
        try {
            $isRead = (int)$this->getRequest()->getParam('is_read');
            $rma->setIsAdminRead($isRead)->save();
            if ($isRead) {
                $message = __('Marked as read');
            } else {
                $message = __('Marked as unread');
            }
            $this->messageManager->addSuccess($message);
        } catch (LocalizedException $e) {
            $this->messageManager->addError($e->getMessage());
        }
        return $resultRedirect->setPath('*/*/edit', ['id' => $rma->getId()]);
    }
}
