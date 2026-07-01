<?php
namespace Custom\PincodeChecker\Observer;
use \Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\App\Request\DataPersistorInterface;
use Magento\Framework\App\ObjectManager;


class PaymentMethodAvailable implements ObserverInterface
{
    /**
     * payment_method_is_active event handler.
     *
     * @param \Magento\Framework\Event\Observer $observer
     */
    protected $_cart;

    protected $helper;


    public function __construct(
        \Psr\Log\LoggerInterface $logger,
        \Custom\PincodeChecker\Helper\Data $helper,
        \Magento\Checkout\Model\Cart $_cart
    )
    {
        $this->_logger = $logger;
        $this->_cart = $_cart;
        $this->helper = $helper;
    }

    public function execute(\Magento\Framework\Event\Observer $observer)
    {  
        $result          = $observer->getEvent()->getResult();
        $method_instance = $observer->getEvent()->getMethodInstance();
        $quote           = $this->_cart->getQuote(); // Updated Code

        $postcode = $quote->getShippingAddress()->getPostcode();
        
        $pincodeStatus = $this->helper->getCashondelivery($postcode);
       
        if($pincodeStatus == 0 )
        {
            if($observer->getEvent()->getMethodInstance()->getCode()=="cashondelivery"){
            $checkResult = $observer->getEvent()->getResult();
            $checkResult->setData('is_available', false); //this is disabling the payment method at checkout page  
            }

        }else{
        if($observer->getEvent()->getMethodInstance()->getCode()=="cashondelivery"){
            $checkResult = $observer->getEvent()->getResult();
            $checkResult->setData('is_available', true); //this is enabling the payment method at checkout page  
        }}
    }
}

