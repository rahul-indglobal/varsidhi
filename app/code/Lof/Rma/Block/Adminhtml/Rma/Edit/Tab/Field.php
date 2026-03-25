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

class Field extends  \Magento\Backend\Block\Widget\Form
{
    public function __construct(
        \Magento\Framework\Data\FormFactory $formFactory,
        \Magento\Backend\Block\Widget\Context $context,
        \Lof\Rma\Helper\Data  $rmaHelper,
        \Magento\Framework\Registry                          $registry,
        array $data = []
    ) {
        $this->formFactory = $formFactory;
        $this->rmaHelper             = $rmaHelper;
        $this->registry             = $registry;
        parent::__construct($context, $data);
    }


    /**
     * General information form
     *
     * @param \Lof\Rma\Api\Data\RmaInterface $rma
     * @return string
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function _prepareForm()
    {
        $form = $this->formFactory->create();

        $this->setForm($form);
        /** @var \Lof\Rma\Model\Rule $rule */
        $rma = $this->registry->registry('current_rma');

        $fieldset = $form->addFieldset('customer_fieldset', ['legend' => __('More Information')]);
        $Fieldcollection = $this->rmaHelper->getFields();
        if ($Fieldcollection) {
        foreach ($Fieldcollection as $field) {
                $fieldset->addField(
                    $field->getCode(),
                    $field->getType(),
                    $this->rmaHelper->getInputParams($field, true, $rma)
                );
            }
        }

        return parent::_prepareForm();
    }
}