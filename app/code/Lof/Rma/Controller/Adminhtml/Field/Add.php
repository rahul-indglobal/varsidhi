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

class Add extends \Lof\Rma\Controller\Adminhtml\Field
{
    /**
     * {@inheritdoc}
     */
    public function execute()
    {
        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        $resultPage = $this->resultFactory->create(ResultFactory::TYPE_PAGE);

        $this->_initField();

        $this->initPage($resultPage);
        $resultPage->getConfig()->getTitle()->prepend(__('New Field'));
        $this->_addBreadcrumb(
            __('Field  Manager'),
            __('Field Manager'),
            $this->getUrl('*/*/')
        );
        $this->_addBreadcrumb(__('Add Field '), __('Add Field'));

        $resultPage->getLayout()
            ->getBlock('head')
            ;
        $this->_addContent($resultPage->getLayout()->createBlock('\Lof\Rma\Block\Adminhtml\Field\Edit'));

        return $resultPage;
    }
}
