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

class Add extends \Lof\Rma\Controller\Adminhtml\Rule
{
    /**
     * {@inheritdoc}
     */
    public function execute()
    {
        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        $resultPage = $this->resultFactory->create(ResultFactory::TYPE_PAGE);

        $this->_initRule();
        $this->initPage($resultPage);
        $resultPage->getConfig()->getTitle()->prepend(__('New Rule'));
        $this->_addBreadcrumb(
            __('Rule  Manager'),
            __('Rule Manager'),
            $this->getUrl('*/*/')
        );
        $this->_addBreadcrumb(__('Add Rule '), __('Add Rule'));

        $resultPage->getLayout()
            ->getBlock('head')
            ;
        $this->_addContent($resultPage->getLayout()->createBlock('\Lof\Rma\Block\Adminhtml\Rule\Edit'))
                ->_addLeft($resultPage->getLayout()->createBlock('\Lof\Rma\Block\Adminhtml\Rule\Edit\Tabs'));
        $resultPage->getLayout()->getBlock('head');

        return $resultPage;
    }
}
