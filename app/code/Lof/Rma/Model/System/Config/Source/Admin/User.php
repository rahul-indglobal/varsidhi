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



namespace Lof\Rma\Model\System\Config\Source\Admin;

use Magento\Framework\Option\ArrayInterface;
use Magento\User\Model\ResourceModel\User\CollectionFactory as UserCollectionFactory;

class User implements ArrayInterface
{
    /**
     * @var UserCollectionFactory
     */
    protected $userCollectionFactory;

    public function __construct(
        UserCollectionFactory $userCollectionFactory
    ) {
        $this->userCollectionFactory = $userCollectionFactory;
    }

    /**
     * To array
     *
     * @return array
     */
    public function toArray()
    {
        $arr = $this->userCollectionFactory->create()->toArray();
        $result = [];
        foreach ($arr['items'] as $value) {
            $result[$value['user_id']] = $value['firstname'] . ' ' . $value['lastname'];
        }

        return $result;
    }

    /**
     * {@inheritdoc}
     * @return array
     */
    public function toOptionArray()
    {
        $result = [];
        foreach ($this->toArray() as $k => $v) {
            $result[] = ['value' => $k, 'label' => $v];
        }

        return $result;
    }
}
