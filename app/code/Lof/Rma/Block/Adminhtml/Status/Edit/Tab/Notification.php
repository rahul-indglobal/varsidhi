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



namespace Lof\Rma\Block\Adminhtml\Status\Edit\Tab;

use Magento\Backend\Block\Widget\Context;
use Magento\Backend\Block\Widget\Form;
use Magento\Framework\Data\FormFactory;
use Magento\Framework\Registry;
use Lof\Rma\Api\Repository\StatusRepositoryInterface;

class Notification extends Form
{
    public function __construct(
         StatusRepositoryInterface $statusRepository,
        FormFactory $formFactory,
        Registry $registry,
        Context $context,
        array $data = []
    ) {
        $this->statusRepository = $statusRepository;
        $this->formFactory = $formFactory;
        $this->registry = $registry;

        parent::__construct($context, $data);
    }

    /**
     * {@inheritdoc}
     */
    protected function _prepareForm()
    {
        $form = $this->formFactory->create();
        $this->setForm($form);
        /** @var \Lof\Rma\Model\Rule $rule */
       $status = $this->registry->registry('current_status');
         $storeId = (int)$this->getRequest()->getParam('store');
         $fieldset = $form->addFieldset('notification_fieldset', ['legend' => __('Notifications')]);
          $customerMessages = $status->getCustomerMessage();

            if(isset($customerMessages[$storeId]))
                $customerMessage = $customerMessages[$storeId];
            else
                $customerMessage = $customerMessages[0];
          $adminMessages = $status->getAdminMessage();

            if(isset($adminMessages[$storeId]))
                $adminMessage = $adminMessages[$storeId];
            else
                $adminMessage = $adminMessages[0];
           $historyMessages = $status->getHistoryMessage();
             if(isset($customerMessages[$storeId]))
                $historyMessage = $historyMessages[$storeId];
            else
                $historyMessage = $historyMessages[0];

        if ($this->_isAllowedAction('Lof_Rma::rma_dictionary_status')) {
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

        $fieldset->addField('customer_message', 'textarea', [
            'label'       => __('Customer message '),
            'name'        => 'customer_message',
            'value'       =>  $customerMessage,
            'note'        => __('Notifications email for customer ,leave blank to not send'),
            'scope_label' => __('[STORE VIEW]'),
            'disabled' => $isElementDisabled
        ]);

        $fieldset->addField('history_message', 'textarea', [
            'label'       => __('History message'),
            'name'        => 'history_message',
            'value'       => $historyMessage,
            'scope_label' => __('[STORE VIEW]'),
            'disabled' => $isElementDisabled
        ]);

        $fieldset->addField('admin_message', 'textarea', [
            'label'       => __('Admin message  '),
            'name'        => 'admin_message',
            'value'       => $adminMessage,
            'note'        => __('Notifications email for admin,leave blank to not send'),
            'scope_label' => __('[STORE VIEW]'),
            'disabled' => $isElementDisabled
        ]);

        return parent::_prepareForm();
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
