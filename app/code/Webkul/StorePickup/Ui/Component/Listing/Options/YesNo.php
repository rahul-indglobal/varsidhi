<?php
/**
 * Webkul Software.
 *
 * @category  Webkul
 * @package   Webkul_StorePickup
 * @author    Webkul
 * @copyright Copyright (c) Webkul Software Private Limited (https://webkul.com)
 * @license   https://store.webkul.com/license.html
 */

namespace Webkul\StorePickup\Ui\Component\Listing\Options;

class YesNo implements \Magento\Framework\Data\OptionSourceInterface
{
    /**
     * @inheritDoc
     */
    public function toOptionArray()
    {
        return [
            [
                'value' => 0,
                'label' => __('No')
            ],
            [
                'value' => 1,
                'label' => __('Yes')
            ]
        ];
    }
}
