<?php


namespace Custom\PincodeChecker\Model\ResourceModel;

class Pincodechecker extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{

    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('pincode_checker', 'pincode_id');
    }
}
