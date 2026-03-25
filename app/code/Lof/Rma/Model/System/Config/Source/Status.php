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



namespace Lof\Rma\Model\System\Config\Source;

use Magento\Eav\Model\Entity\Attribute\Backend\AbstractBackend;

class Status extends AbstractBackend 
{
    /**
     * All options
     *
     * @return array
     */
    public function getAllOptions()
    {
        $options = [
            ['value' => '1', 'label' => __('Yes')],
            ['value' => '0', 'label' => __('No')],
        ];

        return $options;
    }

    /**
     * Get one option label
     *
     * @param string $value
     * @return string|bool
     */
    public function getOptionText($value)
    {
        $options = $this->getAllOptions();
        foreach ($options as $option) {
            if ($option['value'] == $value) {
                return $option['label'];
            }
        }

        return false;
    }
    /************************/
}
