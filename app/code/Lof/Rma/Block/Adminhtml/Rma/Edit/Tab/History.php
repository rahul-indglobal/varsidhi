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


namespace Lof\Rma\Block\Adminhtml\Rma\Edit\Tab;


class History extends \Magento\Backend\Block\Template
{
    public function __construct(
        \Magento\Framework\Registry                          $registry,
        \Magento\Backend\Block\Widget\Context $context,
        array $data = []
    ) {
        $this->registry             = $registry;
        $this->context              = $context;

        parent::__construct($context, $data);
    }

 /**
     * @return \Lof\Rma\Model\Rma
     */
    public function getCurrentRma()
    {
        if ($this->registry->registry('current_rma') && $this->registry->registry('current_rma')->getId()) {
            return $this->registry->registry('current_rma');
        }
    } 
     public function getAttachmentUrl($Uid){
        $this->context->getUrlBuilder()->getUrl('rma/attachment/download',['uid' => $Uid]);
    }
    /**
     * @param bool $isRead
     * @return string
     */
    public function getMarkUrl($isRead)
    {
        return $this->getUrl('*/*/markRead', ['rma_id' => $this->getRma()->getId(), 'is_read' => (int) $isRead]);
    }
}