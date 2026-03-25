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



namespace Lof\Rma\Model\ResourceModel;


class QuickResponse extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    /**
     * {@inheritdoc}
     */
    protected function _construct()
    {
        $this->_init('lof_rma_template', 'template_id');
    }

    /**
     * @param \Magento\Framework\Model\AbstractModel $object
     *
     * @return \Magento\Framework\Model\AbstractModel|\Lof\Rma\Model\QuickResponse
     */
    protected function loadStoreIds(\Magento\Framework\Model\AbstractModel $object)
    {
        /* @var  \Lof\Rma\Model\QuickResponse $object */
        $select = $this->getConnection()->select()
            ->from($this->getTable('lof_rma_template_store'))
            ->where('ts_template_id = ?', $object->getId());
        if ($data = $this->getConnection()->fetchAll($select)) {
            $array = [];
            foreach ($data as $row) {
                $array[] = $row['ts_store_id'];
            }
            $object->setData('store_ids', $array);
        }

        return $object;
    }

    /**
     * @param string $object
     * @return void
     */
    protected function saveStoreIds($object)
    {
        /* @var  \Lof\Rma\Model\QuickResponse $object */
        $condition = $this->getConnection()->quoteInto('ts_template_id = ?', $object->getId());
        $this->getConnection()->delete($this->getTable('lof_rma_template_store'), $condition);
        foreach ((array) $object->getData('store_ids') as $id) {
            $objArray = [
                'ts_template_id' => $object->getId(),
                'ts_store_id' => $id,
            ];
            $this->getConnection()->insert(
                $this->getTable('lof_rma_template_store'),
                $objArray
            );
        }
    }

    /**
     * {@inheritdoc}
     */
    protected function _afterLoad(\Magento\Framework\Model\AbstractModel $object)
    {
        /** @var  \Lof\Rma\Model\QuickResponse $object */
        if (!$object->getIsMassDelete()) {
            $this->loadStoreIds($object);
        }

        return parent::_afterLoad($object);
    }

    /**
     * {@inheritdoc}
     */
    protected function _beforeSave(\Magento\Framework\Model\AbstractModel $object)
    {
        /** @var  \Lof\Rma\Model\QuickResponse $object */
        if (!$object->getId()) {
            $object->setCreatedAt((new \DateTime())->format(\Magento\Framework\Stdlib\DateTime::DATETIME_PHP_FORMAT));
        }
        $object->setUpdatedAt((new \DateTime())->format(\Magento\Framework\Stdlib\DateTime::DATETIME_PHP_FORMAT));

        return parent::_beforeSave($object);
    }

    /**
     * {@inheritdoc}
     */
    protected function _afterSave(\Magento\Framework\Model\AbstractModel $object)
    {
        /** @var  \Lof\Rma\Model\QuickResponse $object */
        if (!$object->getIsMassStatus()) {
            $this->saveStoreIds($object);
        }
        return parent::_afterSave($object);
    }

    /************************/
}
