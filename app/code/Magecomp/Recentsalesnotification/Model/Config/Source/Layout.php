<?php
namespace Magecomp\Recentsalesnotification\Model\Config\Source;

class Layout implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * @var \Magento\Framework\Locale\ListsInterface
     */
    public function __construct(){
		
	}
    public function toOptionArray()
    {
        return [
            ['value' => 'solidcolor', 'label' => __('Solid Color')],
            ['value' => 'image', 'label' => __('Image')],
            ['value' => 'template', 'label' => __('Template')],
        ];
   
    }
}