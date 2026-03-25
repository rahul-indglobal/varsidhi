<?php
namespace Magecomp\Recentsalesnotification\Model\Config\Source;

class Frequancy implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * @var \Magento\Framework\Locale\ListsInterface
     */
    public function __construct(){
		
	}
    public function toOptionArray()
    {
        return [
            ['value' => '20000', 'label' => __('20 Seconds')],
            ['value' => '30000', 'label' => __('30 Seconds')],
            ['value' => '40000', 'label' => __('40 Seconds')],
            ['value' => '50000', 'label' => __('50 Seconds')],						
           
        ];
   
    }
}