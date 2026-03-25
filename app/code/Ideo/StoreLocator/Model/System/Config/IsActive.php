<?php
/**
 * Copyright © 2018 Ideo. All rights reserved.
 * @license GPLv3
 */

namespace Ideo\StoreLocator\Model\System\Config;

use \Magento\Framework\Option\ArrayInterface;
use \Ideo\StoreLocator\Model\Source\IsActive as Source;

class IsActive implements ArrayInterface
{
    /**
     * @var \Ideo\StoreLocator\Model\Source\IsActive
     */
    private $source;

    /**
     * @param \Ideo\StoreLocator\Model\Source\IsActive $source
     */
    public function __construct(Source $source)
    {
        $this->source = $source;
    }

    /**
     * Return array of options as value-label pairs
     *
     * @return array Format: array(array('value' => '<value>', 'label' => '<label>'), ...)
     */
    public function toOptionArray()
    {
        return $this->source->getAvailableStatuses();
    }
}
