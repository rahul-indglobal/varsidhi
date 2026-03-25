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



namespace Lof\Rma\Block\Adminhtml;

use Magento\Backend\Block\Widget\Grid\Container;

class Condition extends Container
{
    /**
     * {@inheritdoc}
     */
    protected function _construct()
    {
        parent::_construct();
        $this->_controller = 'adminhtml_condition';
        $this->_blockGroup = 'Lof_Rma';
        $this->_headerText = __('Conditions');
        $this->_addButtonLabel = __('Add New Condition');
    }

    /**
     * {@inheritdoc}
     */
    public function getCreateUrl()
    {
        return $this->getUrl('*/*/add');
    }
}
