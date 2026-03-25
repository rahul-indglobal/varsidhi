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


namespace Lof\Rma\Api\Repository;

use Magento\Framework\Api\SearchCriteriaInterface;

interface ItemRepositoryInterface
{


    /**
     * Save Item
     * @param \Lof\Rma\Api\Data\ItemInterface $item
     * @return \Lof\Rma\Api\Data\ItemInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function save(\Lof\Rma\Api\Data\ItemInterface $item);

    /**
     * Retrieve Item
     * @param string $itemId
     * @return \Lof\Rma\Api\Data\ItemInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getById($itemId);

    /**
     * Retrieve Item matching the specified criteria.
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @return \Lof\Rma\Api\Data\ItemSearchResultsInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getList(
        \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
    );

    /**
     * Delete Item
     * @param \Lof\Rma\Api\Data\ItemInterface $item
     * @return bool true on success
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function delete(\Lof\Rma\Api\Data\ItemInterface $item);

    /**
     * Delete Item by ID
     * @param string $itemId
     * @return bool true on success
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function deleteById($itemId);
}
