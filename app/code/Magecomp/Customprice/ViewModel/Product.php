<?php 

namespace Magecomp\Customprice\ViewModel;

class Product implements \Magento\Framework\View\Element\Block\ArgumentInterface
{
	protected $helper;

	protected $_coreRegistry;

	protected $_product;

	public function __construct(
       \Magecomp\Customprice\Helper\Data $helper,
       \Magento\Framework\Registry $registry
         )
    {
    	$this->_coreRegistry = $registry;
    	$this->_product = $this->_coreRegistry->registry('product');
        $this->helper = $helper;
    }

    public function getMinprice()
    {
    	$listmode = $this->helper->isProductOption();
    	$customprice = $this->_product->getCustompriceProductwise();
    	$customenable = $this->_product->getCustompriceEnabledisable();
    	$globalprice = $this->helper->getPrice();

    	if($listmode == 0)
    	{
    		$message=$this->helper->getMessage($globalprice);
    		return $message;
    	}
    	if($listmode == 1)
    	{
    		if($customenable == 1)
    		{
    			$message=$this->helper->getMessage($customprice);
    			return $message;
    		}
    	}
    }
}
