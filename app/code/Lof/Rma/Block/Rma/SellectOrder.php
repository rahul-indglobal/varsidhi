<?php
/**
 * Landofcoder
 * 
 * NOTICE OF LICENSE
 * 
 * This source file is subject to the landofcoder.com license that is
 * available through the world-wide-web at this URL:
 * http://landofcoder.com/license
 * 
 * DISCLAIMER
 * 
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 * 
 * @category   Landofcoder
 * @package    Lof_Rma
 * @copyright  Copyright (c) 2016 Landofcoder (http://www.landofcoder.com/)
 * @license    http://www.landofcoder.com/LICENSE-1.0.html
 */

namespace Lof\Rma\Block\Rma;

class SellectOrder extends \Magento\Framework\View\Element\Html\Link
{
    

    /**
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Customer\Model\Session $customerSession
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Customer\Model\Session $customerSession, 
       \Magento\Framework\Api\SearchCriteriaBuilder       $searchCriteriaBuilder,
        \Magento\Framework\Api\SortOrderBuilder            $sortOrderBuilder,
        \Magento\Sales\Api\OrderRepositoryInterface                   $orderRepository,
        \Lof\Rma\Helper\Data    $rmaHelper,
        \Lof\Rma\Helper\Help                                $Helper,
        array $data = []
    ) {
        $this->sortOrderBuilder      = $sortOrderBuilder;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->orderRepository       = $orderRepository;
        $this->rmaHelper             = $rmaHelper;
        $this->helper                = $Helper;
        $this->customerSession               = $customerSession;
         $this->context         = $context;
        parent::__construct($context, $data);
    }

    public function getOrderList() {
        $customer_id = $this->customerSession->getCustomerId();
        $searchCriteria = $this->searchCriteriaBuilder
            ->addFilter('customer_id',(int)$customer_id)
            ->addFilter('entity_id', $this->rmaHelper->getAllowOrderId(), 'in')
            ->addSortOrder($this->sortOrderBuilder
            ->setField('entity_id')
            ->setDirection( \Magento\Framework\Api\SortOrder::SORT_DESC)
            ->create());
            ;

        $orders = $this->orderRepository->getList($searchCriteria->create())->getItems();
        return $orders;
    }
         /**
     * @param int $orderId
     * @return string
     */
    public function getOrderUrl($orderId)
    {
        return  $this->context->getUrlBuilder()->getUrl('sales/order/view', ['order_id' => $orderId]);
    }
    /**
     * @return string
     */
    public function getCreateRmaUrl($order_id)
    {
        return $this->context->getUrlBuilder()->getUrl('returns/rma/new/',['order_id' => $order_id]);
    }

    /**
     * @return int
     */
    public function getReturnPeriod()
    {
        return $this->helper->getConfig($store = null,'rma/policy/return_period');
    }
     /**
     * @return boolean
     */
     public function IsItemsQtyAvailable($order){
        $items = $order->getAllItems();
        foreach ($items as  $item) {
            if($item->getData('base_row_total') <=0||$item->getData('product_type') == 'bundle')  continue; 
                      if($this->rmaHelper->getItemQuantityAvaiable($item)>0){
                         return true;
                      }
                  }  
        return false;    
        }
   
    /**
     * Prepare layout for change buyer
     *
     * @return Object
     */
    public function _prepareLayout() {
        $this->pageConfig->getTitle ()->set(__('Sellect Order'));
        return parent::_prepareLayout ();
    }
    

}