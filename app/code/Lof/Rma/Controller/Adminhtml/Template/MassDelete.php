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


namespace Lof\Rma\Controller\Adminhtml\Template;

use Magento\Framework\Controller\ResultFactory;

class MassDelete extends \Lof\Rma\Controller\Adminhtml\Template
{
    /**
     * {@inheritdoc}
     */
    public function execute()
    {
        /** @var \Magento\Framework\Controller\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);

        $ids = $this->getRequest()->getParam('template_id');
        if (!is_array($ids)) {
            $this->messageManager->addError(__('Please select Template(s)'));
        } else {
            try {
                foreach ($ids as $id) {
                    /** @var \Lof\Rma\Model\QuickResponse $template */
                    $template = $this->templateFactory->create()
                        ->setIsMassDelete(true)
                        ->load($id);
                    $template->delete();
                }
                $this->messageManager->addSuccess(
                    __(
                        'Total of %1 record(s) were successfully deleted',
                        count($ids)
                    )
                );
            } catch (\Magento\Framework\Exception\LocalizedException $e) {
                $this->messageManager->addError($e->getMessage());
            }
        }
        return $resultRedirect->setPath('*/*/index');
    }
}
