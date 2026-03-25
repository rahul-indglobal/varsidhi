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

use Magento\Backend\Block\Widget\Form;

class GeneralInfo extends Form
{
    public function __construct(
        \Magento\Sales\Api\OrderRepositoryInterface          $orderRepository,
        \Magento\Sales\Api\CreditmemoRepositoryInterface     $creditMemoRepository,
        \Magento\Framework\Data\FormFactory                  $formFactory,
        \Magento\Framework\Convert\DataObject                $convertDataObject,
         \Lof\Rma\Model\ResourceModel\Address\Collection     $addressCollection,
        \Lof\Rma\Helper\Help                                 $Helper,
        \Lof\Rma\Helper\Data                                 $dataHelper,
        \Magento\Framework\Registry                          $registry,
        \Magento\Backend\Model\Url                           $backendUrlManager,
        \Magento\Backend\Block\Widget\Context                $context,
        array $data = []
    ) {
        $this->addressCollection    = $addressCollection;
        $this->datahelper            = $dataHelper;
        $this->helper               = $Helper;
        $this->orderRepository      = $orderRepository;
        $this->creditMemoRepository = $creditMemoRepository;
        $this->formFactory          = $formFactory;
        $this->registry             = $registry;
        $this->request =  $context->getRequest();
        $this->backendUrlManager    = $backendUrlManager;
        $this->convertDataObject    = $convertDataObject;

        parent::__construct($context, $data);
    }


    /**
     * General information form
     *
     * @param \Lof\Rma\Api\Data\RmaInterface $rma
     *
     * @return string
     */
    public function _prepareForm()
    {
        $form = $this->formFactory->create();
         $this->setForm($form);
        /** @var \Lof\Rma\Model\Rma $rma */
        $rma = $this->registry->registry('current_rma');
        /** @var \Magento\Framework\Data\Form\Element\Fieldset $fieldset */
        $fieldset = $form->addFieldset('edit_fieldset', ['legend' => __('General Information')]);

        if ($this->_isAllowedAction('Lof_Rma::rma_rma')) {
            $isElementDisabled = false;
        } else {
            $isElementDisabled = true;
        }

        $this->_eventManager->dispatch(
        'lof_check_license',
        ['obj' => $this,'ex'=>'Lof_Rma']
        );

        if ($this->hasData('is_valid') && $this->hasData('local_valid') && !$this->getData('is_valid') && !$this->getData('local_valid')) {
            $isElementDisabled = true;

        }

        if ($rma->getId()) {
            $fieldset->addField('rma_id', 'hidden', [
                'name'  => 'rma_id',
                'value' => $rma->getId(),
            ]);
        }
      
             $fieldset->addField('order_id', 'hidden', [
                'name'  => 'order_id',
                'value' => $this->getOrderId(),
            ]);
        



       if ($rma->getCustomerId()) 
            $fieldset->addField('customer', 'link', [
                'label' => __('Customer'),
                'name'  => 'customer',
                'value' => $this->getOrder()->getCustomerName(),
                'href'  => $this->backendUrlManager->getUrl('customer/index/edit', ['id' => $rma->getCustomerId()]),
            ]);
       
         $fieldset->addField('Customer Email', 'label', [
            'label' => __('Customer Email'),
            'name'  => 'customer_email',
            'value' => $this->getOrder()->getCustomerEmail(),
            'disabled' => $isElementDisabled
        ]);

        $fieldset->addField('order_link', 'link', [
            'label' => __('Order #'),
            'name'  => 'order_id',
            'value' => '#' . $this->getOrder()->getIncrementId(),
            //'href'  => $this->getUrl('sales/order/view', ['order_id' => $rma->getOrderId()]),
            'href'  => $this->getUrl('sales/order/view', ['order_id' => $this->getOrder()->getIncrementId()]),
        ]);

        

        $fieldset->addField('user_id', 'select', [
            'label'  => __('Rma Manager'),
            'name'   => 'user_id',
            'value'  => $rma->getUserId(),
            'values' =>  $this->datahelper->getAdminOptionArray(true),
            'disabled' => $isElementDisabled
        ]);

        $fieldset->addField('status_id', 'select', [
            'label'  => __('Status'),
            'name'   => 'status_id',
            'value'  => $rma->getStatusId(),
            'values' => $this->convertDataObject->toOptionArray($this->datahelper->getStatusList(), "id", "name"),
            'disabled' => $isElementDisabled
        ]);
        $attachments = array();
        if($rma->getId())
           $attachments = $this->datahelper->getAttachments('return_label', $rma->getId());
        
        $fieldset->addField('return_label', 'Lof\Rma\Block\Adminhtml\Rma\Edit\Tab\Element\File', [
            'label'      => __('Return Label'),
            'name'       => 'return_label',
            'attachment' => array_shift( $attachments),
        ]);

        


        if ($this->datahelper->getExchangeOrderIds($rma->getId())) {

            $links = [];
            foreach ($this->datahelper->getExchangeOrderIds($rma->getId()) as $id) {
                $exchageOrder = $this->orderRepository->get($id);
                $links[] = "<a href='" . $this->getUrl(
                        'sales/order/view',
                        ['order_id' => $id]
                    ) . "'>#" . $exchageOrder->getIncrementId() . '</a>';
            }
            $fieldset->addField('exchangeorder', 'note', [
                'label' => __('Exchage Order'),
                'text'  => implode(', ', $links),
            ]);
        }
        if ($this->datahelper->getCreditMemoIds($rma->getId())) {
            $links = [];
            foreach ($this->datahelper->getCreditMemoIds($rma->getId()) as $id) {
                
                $creditmemo = $this->creditMemoRepository->get($id);
                $links[] = "<a href='" . $this->getUrl(
                        'sales/creditmemo/view',
                        ['creditmemo_id' => $id]
                    ) . "'>#" . $creditmemo->getIncrementId() . '</a>';
            }
            $fieldset->addField('credit_memo_id', 'note', [
                'label' => __('Credit Memo'),
                'text'  => implode(', ', $links),
            ]);
        }

        $defaultAddress = $this->helper->getConfig($rma->getStoreId(),'rma/general/return_address');
        
        
        $fieldset->addField('return_address', 'select', [
            'label'  => __('Return Address'),
            'name'   => 'return_address',
            //'value'  => $rma->getaddress()?$rma->getaddress():'DefaultAddress',
            'value'  => $rma->getReturnAddress()?$rma->getReturnAddress():'DefaultAddress',
            'values' => $this->addressCollection->toOptionArray(true, $defaultAddress),
            'disabled' => $isElementDisabled
        ]);

       

        return parent::_prepareForm();
    }
    public function getOrder() {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance ();
        $order = $objectManager->get('Magento\Sales\Model\Order')->load($this->getOrderId());
        return $order;
    }
    public function getOrderId() {
        $rma = $this->registry->registry('current_rma');
        if ($rma->getId()) {
            return $rma->getOrderId();
        }
        $path = trim($this->request->getPathInfo(), '/');
        $params = explode('/', $path);
        //return end($params);
        return $params[5]; //return order_id
    }
    /**
     * Check permission for passed action
     *
     * @param string $resourceId
     * @return bool
     */
    protected function _isAllowedAction($resourceId)
    {
        return $this->_authorization->isAllowed($resourceId);
    }
}