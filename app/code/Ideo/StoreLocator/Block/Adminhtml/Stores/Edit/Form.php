<?php
/**
 * Copyright © 2018 Ideo. All rights reserved.
 * @license GPLv3
 */

namespace Ideo\StoreLocator\Block\Adminhtml\Stores\Edit;

use \Magento\Backend\Block\Widget\Form\Generic;

class Form extends Generic
{
    /**
     * {@inheritdoc}
     */
    protected function _prepareForm()
    {
        /** @var \Magento\Framework\Data\Form $form */
        $form = $this->_formFactory->create(
            [
                'data' => [
                    'id'    => 'edit_form',
                    'action' => $this->getData('action'),
                    'method' => 'post'
                ]
            ]
        );
        $form->setUseContainer(true);
        $this->setForm($form);

        return parent::_prepareForm();
    }
}
