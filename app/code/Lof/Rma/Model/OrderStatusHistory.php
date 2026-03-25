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


namespace Lof\Rma\Model;

use Magento\Framework\DataObject\IdentityInterface;


class OrderStatusHistory extends \Magento\Framework\Model\AbstractModel
    implements \Lof\Rma\Api\Data\OrderStatusHistoryInterface, IdentityInterface
{

     protected function _construct()
    {
        $this->_init('Lof\Rma\Model\ResourceModel\OrderStatusHistory');
    }
    /**
     * {@inheritdoc}
     */
    public function getHistoryId()
    {
        return $this->getData(self::KEY_HISTORY_ID);
    }

    /**
     * {@inheritdoc}
     */
    public function setHistoryId($historyId)
    {
        return $this->setData(self::KEY_HISTORY_ID, $historyId);
    }

    /**
     * {@inheritdoc}
     */
    public function getOrderId()
    {
        return $this->getData(self::KEY_ORDER_ID);
    }

    /**
     * {@inheritdoc}
     */
    public function setOrderId($orderId)
    {
        return $this->setData(self::KEY_ORDER_ID, $orderId);
    }

    /**
     * {@inheritdoc}
     */
    public function getStatus()
    {
        return $this->getData(self::KEY_STATUS);
    }

    /**
     * {@inheritdoc}
     */
    public function setStatus($status)
    {
        return $this->setData(self::KEY_STATUS, $status);
    }

    /**
     * {@inheritdoc}
     */
    public function getCreatedAt()
    {
        return $this->getData(self::KEY_CREATED_AT);
    }

    /**
     * {@inheritdoc}
     */
    public function setCreatedAt($date)
    {
        return $this->setData(self::KEY_CREATED_AT, $date);
    }

    const CACHE_TAG = 'rma_order_status_history';

    /**
     * {@inheritdoc}
     */
    protected $_cacheTag = 'rma_order_status_history';

    /**
     * {@inheritdoc}
     */
    protected $_eventPrefix = 'rma_order_status_history';

    /**
     * {@inheritdoc}
     */
    public function getIdentities()
    {
        return [self::CACHE_TAG.'_'.$this->getId()];
    }

    /**
     * {@inheritdoc}
     */

}
