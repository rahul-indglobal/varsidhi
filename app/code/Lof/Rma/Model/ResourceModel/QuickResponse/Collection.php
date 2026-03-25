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



namespace Lof\Rma\Model\ResourceModel\QuickResponse;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    /**
     * {@inheritdoc}
     */
    protected function _construct()
    {
        $this->_init('Lof\Rma\Model\QuickResponse', 'Lof\Rma\Model\ResourceModel\QuickResponse');
    }

    /**
     * {@inheritdoc}
     */
    public function toOptionArray($emptyOption = false)
    {
        $arr = [];
        if ($emptyOption) {
            $arr[0] = ['value' => 0, 'label' => __('-- Please Select --')];
        }
        /** @var \Lof\Rma\Model\QuickResponse $item */
        foreach ($this as $item) {
            $arr[] = ['value' => $item->getId(), 'label' => $item->getName()];
        }

        return $arr;
    }

    /**
     * @param string|false $emptyOption
     *
     * @return array
     */
    public function getOptionArray($emptyOption = false)
    {
        $arr = [];
        if ($emptyOption) {
            $arr[0] = __('-- Please Select --');
        }
        /** @var \Lof\Rma\Model\QuickResponse $item */
        foreach ($this as $item) {
            $arr[$item->getId()] = $item->getName();
        }

        return $arr;
    }

    /**
     * @param string $storeId
     *
     * @return $this
     */
    public function addStoreFilter($storeId)
    {
        $this->getSelect()
            ->where("EXISTS (SELECT * FROM `{$this->getTable('lof_rma_template_store')}`
                AS `template_store_table`
                WHERE main_table.template_id = template_store_table.ts_template_id
                AND template_store_table.ts_store_id in (?))", [0, $storeId]);

        return $this;
    }

     /************************/
}
