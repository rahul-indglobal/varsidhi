<?php
/**
 * Copyright © 2018 Ideo. All rights reserved.
 * @license GPLv3
 */

namespace Ideo\StoreLocator\Block\Adminhtml\Stores\Edit\Tab;

use \Magento\Backend\Block\Widget\Form\Generic;
use \Magento\Backend\Block\Template\Context;
use \Magento\Framework\Registry;
use \Magento\Framework\Data\FormFactory;
use \Ideo\StoreLocator\Model\System\Config\IsActive;
use \Ideo\StoreLocator\Model\StoreLocator\System\Config\Categories;
use \Ideo\StoreLocator\Model\Config\Source\Country;

class Info extends Generic
{
    /**
     * @var IsActive
     */
    private $isActive;

    /**
     * @var Categories
     */
    private $categories;

    /**
     * @var Country
     */
    private $country;

    /**
     * Info constructor.
     * @param Context $context
     * @param Registry $registry
     * @param FormFactory $formFactory
     * @param IsActive $isActive
     * @param Categories $categories
     * @param Country $country
     * @param array $data
     */
    public function __construct(
        Context $context,
        Registry $registry,
        FormFactory $formFactory,
        IsActive $isActive,
        Categories $categories,
        Country $country,
        array $data = []
    ) {
        $this->isActive = $isActive;
        $this->categories = $categories;
        $this->country = $country;

        parent::__construct($context, $registry, $formFactory, $data);
    }

    /**
     * View URL getter
     *
     * @param int $storeId
     *
     * @return string
     */
    public function getViewUrl($storeId)
    {
        return $this->getUrl('storelocator/*/*', ['store_id' => $storeId]);
    }

    /**
     * @return $this
     */
    protected function _prepareForm()
    {
        $model = $this->_coreRegistry->registry('storelocator_store');

        $form = $this->_formFactory->create();

        $fieldset = $form->addFieldset(
            'base_fieldset',
            ['legend' => __('Informations')]
        );

        if ($model->getId()) {
            $fieldset->addField(
                'store_id',
                'hidden',
                ['name' => 'store_id']
            );
        }

        $fieldset->addField(
            'category_id',
            'select',
            [
                'name'     => 'category_id',
                'label'    => __('Category'),
                'options'  => $this->categories->toOptionArray(),
                'required' => true
            ]
        );

        $fieldset->addField(
            'name',
            'text',
            [
                'name'     => 'name',
                'label'    => __('Name'),
                'required' => true
            ]
        );

        $fieldset->addField(
            'address',
            'textarea',
            [
                'name'     => 'address',
                'label'    => __('Address'),
                'required' => true
            ]
        );

        $fieldset->addField(
            'city',
            'text',
            [
                'name'     => 'city',
                'label'    => __('City'),
                'required' => false
            ]
        );

        $fieldset->addField(
            'postcode',
            'text',
            [
                'name'     => 'postcode',
                'label'    => __('Zip Code'),
                'required' => false
            ]
        );

        $fieldset->addField(
            'country',
            'select',
            [
                'name'     => 'country',
                'label'    => __('Country'),
                'options'  => $this->country->toOptionArray(),
                'required' => true
            ]
        );

        $fieldset->addField(
            'email',
            'text',
            [
                'name'     => 'email',
                'label'    => __('E-mail'),
                'required' => false
            ]
        );

        $fieldset->addField(
            'phone',
            'text',
            [
                'name'     => 'phone',
                'label'    => __('Phone Number'),
                'required' => false
            ]
        );

        $fieldset->addField(
            'fax',
            'text',
            [
                'name'     => 'fax',
                'label'    => __('Fax'),
                'required' => false
            ]
        );

        $fieldset->addField(
            'website',
            'text',
            [
                'name'     => 'website',
                'label'    => __('Website'),
                'required' => false
            ]
        );

        $fieldset->addField(
            'is_active',
            'select',
            [
                'label'   => __('Status'),
                'title'   => __('Status'),
                'name'    => 'is_active',
                'options' => $this->isActive->toOptionArray()
            ]
        );

        if (!$model->getId()) {
            $model->setData('is_active', '1');
        }

        $data = $model->getData();
        $form->setValues($data);
        $this->setForm($form);

        return parent::_prepareForm();
    }
}
