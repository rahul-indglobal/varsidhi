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
 * Interface for return address search results.
 */
interface AddressSearchResultsInterface extends \Magento\Framework\Api\SearchResultsInterface
{
    /**
     * Get return address list.
     *
     * @return \Lof\Rma\Api\Data\AddressInterface[]
     */
    public function getItems();

    /**
     * Set return address list.
     *
     * @param array $items Array of \Lof\Rma\Api\Data\AddressInterface[]
     * @return $this
     */
    public function setItems(array $items);
}
