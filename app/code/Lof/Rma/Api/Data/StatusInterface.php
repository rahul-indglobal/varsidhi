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

use Lof\Rma\Api;

interface StatusInterface extends DataInterface
{
    const APPROVED     = 'approved';
    const PACKAGE_SENT = 'package_sent';
    const REJECTED     = 'rejected';
    const CLOSED       = 'closed';

    const KEY_NAME             = 'name';
    const KEY_SORT_ORDER       = 'sort_order';
    const KEY_IS_SHOW_SHIPPING = 'is_show_shipping';
    const KEY_CUSTOMER_MESSAGE = 'customer_message';
    const KEY_ADMIN_MESSAGE    = 'admin_message';
    const KEY_HISTORY_MESSAGE  = 'history_message';
    const KEY_IS_ACTIVE        = 'is_active';
    const KEY_CODE             = 'code';

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
     * @return int
     */
    public function getSortOrder();

    /**
     * @param int $sortOrder
     * @return $this
     */
    public function setSortOrder($sortOrder);

    /**
     * @return bool|null
     */
    public function getIsShowShipping();

    /**
     * @param bool $isShowShipping
     * @return $this
     */
    public function setIsShowShipping($isShowShipping);

    /**
     * @return string
     */
    public function getCustomerMessage();

    /**
     * @param string $customerMessage
     * @return $this
     */
    public function setCustomerMessage($customerMessage);

    /**
     * @return string
     */
    public function getAdminMessage();

    /**
     * @param string $adminMessage
     * @return $this
     */
    public function setAdminMessage($adminMessage);

    /**
     * @return string
     */
    public function getHistoryMessage();

    /**
     * @param string $historyMessage
     * @return $this
     */
    public function setHistoryMessage($historyMessage);

    /**
     * @return bool|null
     */
    public function getIsActive();

    /**
     * @param bool $isActive
     * @return $this
     */
    public function setIsActive($isActive);

    /**
     * @return string
     */
    public function getCode();

    /**
     * @param string $code
     * @return $this
     */
    public function setCode($code);
}