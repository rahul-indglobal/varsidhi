<?php
namespace Custom\PincodeChecker\Model\ResourceModel\Pincodechecker;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{

    /**
     * Define resource model
     *
     * @return void
     */
    protected $_idFieldName = 'pincode_id';

    protected function _construct()
    {
        $this->_init(
            'Custom\PincodeChecker\Model\Pincodechecker',
            'Custom\PincodeChecker\Model\ResourceModel\Pincodechecker'
        );
    }
}
