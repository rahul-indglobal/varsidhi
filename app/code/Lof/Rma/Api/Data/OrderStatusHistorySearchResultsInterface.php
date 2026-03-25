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

interface OrderStatusHistorySearchResultsInterface extends \Magento\Framework\Api\SearchResultsInterface
{


    /**
     * Get OrderStatusHistory list.
     * @return \Lofmp\Rma\Api\Data\OrderStatusHistoryInterface[]
     */
    public function getItems();

    /**
     * Set history_id list.
     * @param \Lofmp\Rma\Api\Data\OrderStatusHistoryInterface[] $items
     * @return $this
     */
    public function setItems(array $items);
}