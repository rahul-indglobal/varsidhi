<?php 
namespace Custom\PincodeChecker\Observer;

use Magento\Framework\Event\ObserverInterface;
use Psr\Log\LoggerInterface;

class OrderPlacebefore implements ObserverInterface
{
    /**
     * @param Observer $observer
     * @return void
     */

    protected $logger;
    protected $helper;
    protected $_messageManager;
 
     public function __construct(LoggerInterface$logger,
        \Magento\Framework\Message\ManagerInterface $messageManager,
        \Custom\PincodeChecker\Helper\Data $helper
     ) {
     $this->logger = $logger;
     $this->helper = $helper;
     $this->_messageManager = $messageManager;
     }


    public function execute(\Magento\Framework\Event\Observer $observer)
    {   
            $order = $observer->getEvent()->getOrder();
            $postcode = $order->getBillingAddress()->getPostcode();
            $pincodeStatus = $this->helper->getCashondelivery($postcode);
            $shippingStatus = $this->helper->getShipping($postcode);

            if($pincodeStatus == 0 && $shippingStatus == 0)
            {
                $this->_messageManager->addError(__("Order is not placed"));
                exit;

            }

    }
}