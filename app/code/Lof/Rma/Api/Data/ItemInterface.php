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



namespace Lof\Rma\Api\Data;

/**
 * @method \Lof\Rma\Api\Data\ItemInterface setIsRmaAllowed(bool $flag)
 * @method bool getIsRmaAlloed()
 * @method \Lof\Rma\Api\Data\ItemInterface setQtyAvailable(int $flag)
 * @method int getQtyAvailable()
 */
interface ItemInterface extends DataInterface
{
    const KEY_RMA_ID = 'rma_id';
    const KEY_ORDER_ITEM_ID = 'order_item_id';
    const KEY_PRODUCT_ID = 'product_id';
    const KEY_ORDER_ID = 'order_id';
    const KEY_REASON_ID = 'reason_id';
    const KEY_RESOLUTION_ID = 'resolution_id';
    const KEY_CONDITION_ID = 'condition_id';
    const KEY_QTY_REQUESTED = 'qty_requested';
    const KEY_CREATED_AT = 'created_at';
    const KEY_UPDATED_AT = 'updated_at';
    const KEY_NAME = 'name';
    const KEY_PRODUCT_OPTIONS = 'product_options';
    const KEY_TO_STOCK = 'to_stock';

    /**
     * @return int
     */
    public function getRmaId();

    /**
     * @param int $rmaId
     * @return $this
     */
    public function setRmaId($rmaId);

    /**
     * @return int
     */
    public function getOrderItemId();

    /**
     * @param int $orderItemId
     * @return $this
     */
    public function setOrderItemId($orderItemId);

    /**
     * @return int
     */
    public function getProductId();

    /**
     * @param int $id
     * @return $this
     */
    public function setProductId($id);

    /**
     * @return int
     */
    public function getOrderId();

    /**
     * @param int $orderId
     * @return $this
     */
    public function setOrderId($orderId);

    /**
     * @return int
     */
    public function getReasonId();

    /**
     * @param int $reasonId
     * @return $this
     */
    public function setReasonId($reasonId);

    /**
     * @return int
     */
    public function getResolutionId();

    /**
     * @param int $resolutionId
     * @return $this
     */
    public function setResolutionId($resolutionId);

    /**
     * @return int
     */
    public function getConditionId();

    /**
     * @param int $conditionId
     * @return $this
     */
    public function setConditionId($conditionId);

    /**
     * @return int
     */
    public function getQtyRequested();

    /**
     * @param int $qtyRequested
     * @return $this
     */
    public function setQtyRequested($qtyRequested);

    /**
     * @return string
     */
    public function getCreatedAt();

    /**
     * @param string $createdAt
     * @return $this
     */
    public function setCreatedAt($createdAt);

    /**
     * @return string
     */
    public function getUpdatedAt();

    /**
     * @param string $updatedAt
     * @return $this
     */
    public function setUpdatedAt($updatedAt);

    /**
     * @return string
     */
    public function getName();

    /**
     * @param string $name
     * @return $this
     */
    public function setName($name);

    /**
     * @return array
     */
    public function getProductOptions();

    /**
     * @param array $options
     * @return $this
     */
    public function setProductOptions($options);

    /**
     * @return bool|null
     */
    public function getToStock();

    /**
     * @param bool $isToStock
     * @return $this
     */
    public function setToStock($isToStock);
}