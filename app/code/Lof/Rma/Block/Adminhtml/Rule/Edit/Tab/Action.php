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
use \Lof\Rma\Helper\Data  as DataHelper;
use Lof\Rma\Model\ResourceModel\Status\CollectionFactory as StatusCollectionFactory;

class Action extends Form
{
    public function __construct(
        StatusCollectionFactory $statusCollectionFactory,
        DataHelper $dataHelper,
        FormFactory $formFactory,
        Registry $registry,
        Context $context,
        array $data = []
    ) {
        $this->statusCollectionFactory = $statusCollectionFactory;
        $this->dataHelper = $dataHelper;
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

        $fieldset = $form->addFieldset('action_fieldset', ['legend' => __('Actions')]);
        if ($rule->getId()) {
            $fieldset->addField('rule_id', 'hidden', [
                'name'  => 'rule_id',
                'value' => $rule->getId(),
            ]);
        }
        $fieldset->addField('status_id', 'select', [
            'label'  => __('Set Status'),
            'name'   => 'status_id',
            'value'  => $rule->getStatusId(),
            'values' => $this->statusCollectionFactory->create()->toOptionArray(true),
            'disabled' => $isElementDisabled
        ]);
        $fieldset->addField('user_id', 'select', [
            'label'  => __('Set Owner'),
            'name'   => 'user_id',
            'value'  => $rule->getUserId(),
            'values' => $this->dataHelper->getAdminOptionArray(true),
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
