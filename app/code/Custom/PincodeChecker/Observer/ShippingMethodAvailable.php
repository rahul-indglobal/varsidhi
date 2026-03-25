<?php
namespace Custom\PincodeChecker\Observer;
use \Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\App\Request\DataPersistorInterface;
use Magento\Framework\App\ObjectManager;


class ShippingMethodAvailable implements ObserverInterface
{
	protected $_cart;
	protected $_helper;

	public function __construct(
        \Psr\Log\LoggerInterface $logger,
        \Custom\PincodeChecker\Helper\Data $helper,
        \Magento\Checkout\Model\Cart $_cart
    )
    {
    	$this->_logger = $logger;
    	$this->_cart = $cart;
    	$this->_helper=$helper;

    }

    public function execute(\Magento\Framework\Event\Observer $observer)
    {
    	$result          = $observer->getEvent()->getResult();
    	$method_instance =$observer->getEvent()->getMethodInstance();
    	$quote 			 =$this->_cart->getQuote();

    	$postcode = $quote->getShipping()->getShippingMethod()->getPostcode();
        
        $shippingStatus = $this->helper->getShipping($postcode);

        if($shippingStatus == 0 )
        {
        	
            if($observer->getEvent()->getMethodInstance()->getCode()=="shipping")
            {
            $checkResult = $observer->getEvent()->getResult();
            $checkResult->setData('Yes', false); //enable shipping at cart page during checkout
            }

        }else{
        if($observer->getEvent()->getMethodInstance()->getCode()=="shipping")
        {
            $checkResult = $observer->getEvent()->getResult();
            $checkResult->setData('No', true); //disable shipping at the cart page during checkout

        }
    }
    }
}