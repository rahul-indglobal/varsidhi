<?php
namespace Custom\PincodeChecker\Api;
use Magento\Framework\Api\SearchCriteriaInterface;

interface PincodecheckerRepositoryInterface
{


    /**
     * Save pincodechecker
     * @param \Custom\PincodeChecker\Api\Data\PincodecheckerInterface $pincodechecker
     * @return \Custom\PincodeChecker\Api\Data\PincodecheckerInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    
    public function save(
        \Custom\PincodeChecker\Api\Data\PincodecheckerInterface $pincodechecker
    );

    /**
     * Retrieve pincodechecker
     * @param string $pincodecheckerId
     * @return \Custom\PincodeChecker\Api\Data\PincodecheckerInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    
    public function getById($pincodecheckerId);

    /**
     * Retrieve pincodechecker matching the specified criteria.
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @return \Custom\PincodeChecker\Api\Data\PincodecheckerSearchResultsInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    
    public function getList(
        \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
    );

    /**
     * Delete pincodechecker
     * @param \Custom\PincodeChecker\Api\Data\PincodecheckerInterface $pincodechecker
     * @return bool true on success
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    
    public function delete(
        \Custom\PincodeChecker\Api\Data\PincodecheckerInterface $pincodechecker
    );

    /**
     * Delete pincodechecker by ID
     * @param string $pincodecheckerId
     * @return bool true on success
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    
    public function deleteById($pincodecheckerId);
}
