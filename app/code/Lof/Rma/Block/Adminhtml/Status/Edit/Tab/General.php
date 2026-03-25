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

class General extends Form
{
    public function __construct(
        FormFactory $formFactory,
        Registry $registry,
        Context $context,
        array $data = []
    ) {
        $this->formFactory = $formFactory;
        $this->registry = $registry;

        parent::__construct($context, $data);
    }

    /**
     * {@inheritdoc}
     */
    protected function _prepareForm()
    {
        $storeId = (int)$this->getRequest()->getParam('store');
        $form = $this->formFactory->create();
        $this->setForm($form);
        /** @var \Lof\Rma\Model\Rule $rule */
        $status = $this->registry->registry('current_status');

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

       $fieldset = $form->addFieldset('edit_fieldset', ['legend' => __('General Information')]);
        if ($status->getId()) {
            $fieldset->addField('status_id', 'hidden', [
                'name'  => 'status_id',
                'value' => $status->getId(),
            ]);
        }

        $fieldset->addField('store_id', 'hidden', [
            'name'  => 'store_id',
            'value' => $storeId,
        ]);

        $fieldset->addField('name', 'text', [
            'label'       => __('Title'),
            'name'        => 'name',
            'value'       => $status->getName(),
            'required'    => true,
            'scope_label' => __('[STORE VIEW]'),
            'disabled' => $isElementDisabled
        ]);

        $fieldset->addField('code', 'text', [
            'label'    => __('Code'),
            'name'     => 'code',
            'value'    => $status->getCode(),
            'disabled' => (!$isElementDisabled  || ($status->getCode() == '')) ? false : true,
            'required' => true,
        ]);

        $fieldset->addField('sort_order', 'text', [
            'label' => __('Sort Order'),
            'name'  => 'sort_order',
            'value' => $status->getSortOrder(),
            'disabled' => $isElementDisabled
        ]);

        $fieldset->addField('is_active', 'select', [
            'label'  => __('Is Active'),
            'name'   => 'is_active',
            'value'  => $status->getIsActive(),
            'values' => [0 => __('No'), 1 => __('Yes')],
            'disabled' => $isElementDisabled
        ]);

        $fieldset->addField('is_show_shipping', 'select', [
            'label'  => __('Show shipping'),
            'name'   => 'is_show_shipping',
            'value'  => $status->getIsShowShipping(),
            'values' => [0 => __('No'), 1 => __('Yes')],
            'note'    => __('Show shipping buttons \'Print RMA Packing Slip\','.
                ' \'Print RMA Shipping Label\' and \'Confirm Shipping\' in the customer account'),
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
