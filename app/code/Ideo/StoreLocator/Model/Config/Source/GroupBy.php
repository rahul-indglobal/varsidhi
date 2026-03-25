<?php
/**
 * Copyright © 2018 Ideo. All rights reserved.
 * @license GPLv3
 */

namespace Ideo\StoreLocator\Model\Config\Source;

use \Magento\Framework\Option\ArrayInterface;

class GroupBy implements ArrayInterface
{
    const DONT_GROUP = 0;
    const COUNTRY = 1;
    const CATEGORY = 2;
    const COUNTRY_CATEGORY = 3;
    const CATEGORY_COUNTRY = 4;

    /**
     * @return array
     */
    public function toOptionArray()
    {
        return [
            ['value' => self::DONT_GROUP, 'label' => 'Don\'t group'],
            ['value' => self::COUNTRY, 'label' => 'Country'],
            ['value' => self::CATEGORY, 'label' => 'Category'],
            ['value' => self::COUNTRY_CATEGORY, 'label' => 'Country -> Category'],
            ['value' => self::CATEGORY_COUNTRY, 'label' => 'Category -> Country'],
        ];
    }
}
