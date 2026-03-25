<?php
namespace Magecomp\Recentsalesnotification\Model\Config\Source;

class Position implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * @var \Magento\Framework\Locale\ListsInterface
     */
    public function __construct(){
		
	}
    public function toOptionArray()
    {
        return [
            ['value' => 'top_left', 'label' => __('Top Left')],
            ['value' => 'top_right', 'label' => __('Top Right')],
            ['value' => 'bottom_left', 'label' => __('Bottom Left')],
            ['value' => 'bottom_right', 'label' => __('Bottom Right')],						
           
        ];
   
    }
}