<?php
namespace Magecomp\Sms\Observer\Customer;

use Magento\Framework\Event\ObserverInterface;

class ShipmentSaveObserver implements ObserverInterface
{
    protected $helperapi;
    protected $helpershipment;
    protected $emailfilter;
    protected $customerFactory;
    protected $pricehelper;
    protected $orderdata;

    public function __construct(
        \Magecomp\Sms\Helper\Apicall $helperapi,
        \Magecomp\Sms\Helper\Shipment $helpershipment,
        \Magento\Email\Model\Template\Filter $filter,
        \Magento\Framework\Pricing\Helper\Data $pricehelper,
        \Magento\Sales\Model\Order $orderdata,
        \Magento\Customer\Model\CustomerFactory $customerFactory,
        \Magento\Framework\App\RequestInterface $request)
    {
        $this->helperapi = $helperapi;
        $this->helpershipment = $helpershipment;
        $this->emailfilter = $filter;
        $this->pricehelper = $pricehelper;
        $this->orderdata = $orderdata;
        $this->customerFactory = $customerFactory;
        $this->_request = $request;
    }

    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        if(!$this->helpershipment->isEnabled())
            return $this;

        $shipment   = $observer->getShipment();
        $order      = $shipment->getOrder();
        $currOrder = $this->orderdata->load($order->getId());
        $shipmentCollection = $currOrder->getShipmentsCollection();

        $post = $this->_request->getPost();
        if(count($shipmentCollection)>0)
        {
            $carrier_title = $carrier_number  = "";
            if($post->tracking and count($post->tracking) > 0){
                $carrier_title = $post->tracking[1]['title'];
                $carrier_number = $post->tracking[1]['number'];
            }

            $billingAddress = $order->getBillingAddress();
            $mobilenumber = $billingAddress->getTelephone();

            if($order->getCustomerId() > 0)
            {
                $customer = $this->customerFactory->create()->load($order->getCustomerId());
                $mobile = $customer->getMobilenumber();
                if($mobile != '' && $mobile != null)
                {
                    $mobilenumber = $mobile;
                }

                $this->emailfilter->setVariables([
                    'order' => $order,
                    'shipment' => $shipment,
                    'customer' => $customer,
                    'mobilenumber' => $mobilenumber,
                    'shippingcarrier' => $carrier_title,
                    'trackingnumber' => $carrier_number
                ]);
            }
            else
            {
                $this->emailfilter->setVariables([
                    'order' => $order,
                    'shipment' => $shipment,
                    'mobilenumber' => $mobilenumber,
                    'shippingcarrier' => $carrier_title,
                    'trackingnumber' => $carrier_number
                ]);
            }

            if ($this->helpershipment->isShipmentNotificationForUser($order->getStoreId()))
            {
                $message = $this->helpershipment->getShipmentNotificationUserTemplate($order->getStoreId());

                $orderTotal=$this->pricehelper->currency($order->getGrandTotal(), true, false);
                $codes = array('{{var order.grand_total}}');
                $accurate = array($orderTotal);

                $message=str_replace($codes,$accurate,$message);

                $finalmessage = $this->emailfilter->filter($message);
                $this->helperapi->callApiUrl($mobilenumber,$finalmessage);
            }

            if($this->helpershipment->isShipmentNotificationForAdmin($order->getStoreId()) && $this->helpershipment->getAdminNumber())
            {
                $message = $this->helpershipment->getShipmentNotificationForAdminTemplate($order->getStoreId());
                $finalmessage = $this->emailfilter->filter($message);
                $this->helperapi->callApiUrl($this->helpershipment->getAdminNumber(),$finalmessage);
            }
        }
        return $this;
    }
}
