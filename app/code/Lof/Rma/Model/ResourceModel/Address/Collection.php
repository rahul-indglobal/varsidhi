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


namespace Lof\Rma\Model\ResourceModel\Address;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    /**
     * {@inheritdoc}
     */
    protected function _construct()
    {
        $this->_init('Lof\Rma\Model\Address', 'Lof\Rma\Model\ResourceModel\Address');
    }

    /**
     * {@inheritdoc}
     */
    public function toOptionArray($emptyOption = false, $defaultAddress = '')
    {
        $arr = [];
        if ($emptyOption) {
            $defaultLabel = __('-- Default Address --');
            if (empty($defaultAddress)) {
                $defaultLabel = __('-- Please Select --');
                $defaultAddress = 0;
            }
            $arr[0] = ['value' => $defaultAddress, 'label' => $defaultLabel];
        }
        /** @var \Lof\Rma\Model\Address $item */
        foreach ($this->addActiveFilter() as $item) {
            //$arr[] = ['value' => $item->getAddress(), 'label' => $item->getName()];
            $arr[] = ['value' => $item->getAddressId(), 'label' => $item->getName()];
        }

        return $arr;
    }

    /**
     * @return $this
     */
    public function addActiveFilter()
    {
        $this->getSelect()
            ->where('is_active', 1)
        ;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    protected function _initSelect()
    {
        parent::_initSelect();

        $this->addOrder('sort_order', self::SORT_ORDER_ASC);

        return $this;
    }

     /************************/
}
