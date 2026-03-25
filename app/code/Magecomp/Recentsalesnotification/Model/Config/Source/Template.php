<?php
namespace Magecomp\Recentsalesnotification\Model\Config\Source;

class Template implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * @var \Magento\Framework\Locale\ListsInterface
     */
    public function __construct(){
		
	}
    public function toOptionArray()
    {
        return [
            ['value' => 'template1', 'label' => __('BLACK FRIDAY')],
            ['value' => 'template2', 'label' => __('CHRISTMAS')],
            ['value' => 'template3', 'label' => __('DESIGN')],
			['value' => 'template4', 'label' => __('HALLOWEEN')],
			['value' => 'template5', 'label' => __('NEW YEAR')],
			['value' => 'template6', 'label' => __('THANKS GIVING')],
			['value' => 'template7', 'label' => __('VALENTINE DAY')],
        ];
   
    }
}