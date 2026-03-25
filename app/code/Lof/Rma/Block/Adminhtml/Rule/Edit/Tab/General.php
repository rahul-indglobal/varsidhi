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



namespace Lof\Rma\Block\Adminhtml\Rule\Edit\Tab;

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
        $form = $this->formFactory->create();
        $this->setForm($form);
        /** @var \Lof\Rma\Model\Rule $rule */
        $rule = $this->registry->registry('current_rule');
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

        $fieldset = $form->addFieldset('edit_fieldset', ['legend' => __('General Information')]);
        if ($rule->getId()) {
            $fieldset->addField('rule_id', 'hidden', [
                'name'  => 'rule_id',
                'value' => $rule->getId(),
            ]);
        }
        $fieldset->addField('name', 'text', [
            'label'    => __('Rule Name'),
            'required' => true,
            'name'     => 'name',
            'value'    => $rule->getName(),
            'disabled' => $isElementDisabled
        ]);
        $fieldset->addField('is_active', 'select', [
            'label'    => __('Is Active'),
            'required' => true,
            'name'     => 'is_active',
            'value'    => $rule->getIsActive(),
            'values'   => [0 => __('No'), 1 => __('Yes')],
            'disabled' => $isElementDisabled
        ]);
        $fieldset->addField('sort_order', 'text', [
            'label' => __('Priority'),
            'name'  => 'sort_order',
            'value' => $rule->getSortOrder(),
            'note'  => __('Arranged in the ascending order. 0 is the highest.'),
            'disabled' => $isElementDisabled
        ]);
        $fieldset->addField('is_stop_processing', 'select', [
            'label'  => __('Is Stop Processing'),
            'name'   => 'is_stop_processing',
            'value'  => $rule->getIsStopProcessing(),
            'values' => [0 => __('No'), 1 => __('Yes')],
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
