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


class Items extends \Magento\Backend\Block\Template
{

    public function __construct(
        \Lof\Rma\Helper\Data $dataHelper,
        \Magento\Framework\Registry $registry,
        \Magento\Backend\Block\Widget\Context $context,
        array $data = []
    ) {

         $this->registry            = $registry;
         $this->request =  $context->getRequest();
        $this->dataHelper             = $dataHelper;

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

    public function getOrder() {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance ();
        $order = $objectManager->get('Magento\Sales\Model\Order')->load($this->getOrderId());
        return $order;
    }
      public function getOrderId() {
        if($this->getCurrentRma())
         return $this->getCurrentRma()->getOrderId();
        
        $path = trim($this->request->getPathInfo(), '/');
        $params = explode('/', $path);
        return end($params);
    }

      public function getRmaItemData($item)
    {
        if ($this->getCurrentRma()) 
             return $this->dataHelper->getRmaItemData($item,$this->getCurrentRma()->getId());
       
    }

    /**
     * @return \Lof\Rma\Api\Data\ReturnInterface[]
     */
    public function getConditions()
    {
        return $this->dataHelper->getConditions();
    }

    /**
     * @return \Lof\Rma\Api\Data\ReturnInterface[]
     */
    public function getResolutions()
    {
        return $this->dataHelper->getResolutions();
    }

    /**
     * @return \Lof\Rma\Api\Data\ReturnInterface[]
     */
    public function getReasons()
    {
        return $this->dataHelper->getReasons();
    }

    public function getQtyAvailable($item)
    {
        return $this->dataHelper->getItemQuantityAvaiable($item);
    }
   
    public function getQtyRequest($item)
    {
         if ($this->getCurrentRma()) 
        return $this->dataHelper->getQtyReturnedRma($item,$this->getCurrentRma()->getId());
    }
    

     public function getAttachmentUrl($Uid){
        $this->context->getUrlBuilder()->getUrl('rma/attachment/download',['uid' => $Uid]);
    }
}