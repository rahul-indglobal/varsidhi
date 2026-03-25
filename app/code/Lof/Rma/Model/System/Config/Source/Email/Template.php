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



namespace Lof\Rma\Model\System\Config\Source\Email;

use Magento\Config\Model\Config\Source\Email\Template as MailTemplate;

class Template extends MailTemplate
{
    /**
     * {@inheritdoc}
     */
    public function toOptionArray()
    {
        $options = parent::toOptionArray();
        array_unshift(
            $options,
            [
                'value' => 'none',
                'label' => __('- Disable these emails -'),
            ]
        );

        return $options;
    }
}
