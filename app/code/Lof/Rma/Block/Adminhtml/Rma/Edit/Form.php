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



namespace Lof\Rma\Block\Adminhtml\Rma\Edit;


use Magento\Backend\Block\Widget\Context;
use Magento\Backend\Block\Widget\Form as WidgetForm;
use Magento\Framework\Data\FormFactory;

class Form extends WidgetForm
{
    public function __construct(
        FormFactory $formFactory,
        Context $context,
        array $data = []
    ) {
        $this->formFactory = $formFactory;

        parent::__construct($context, $data);
    }

    /**
     * Old exchange amount.
     *
     * @var int
     */
    protected $oldAmount;

    /**
     * New exchange amount.
     *
     * @var int
     */
    protected $newAmount;


    /**
     * {@inheritdoc}
     */
    protected function _construct()
    {
       $form = $this->formFactory->create()->setData([
            'id'      => 'edit_form',
            'action'  => $this->getUrl('*/*/save', ['id' => $this->getRequest()->getParam('id')]),
            'method'  => 'post',
            'enctype' => 'multipart/form-data',
        ]);

        $form->setUseContainer(true);
        $this->setForm($form);
     /*   $amounts = $this->calculateHelper->calculateExchangeAmounts($this->getRma());

        $this->oldAmount = $amounts['oldAmount'];
        $this->newAmount = $amounts['newAmount'];*/

        return parent::_prepareForm();
        
    }

    

}
