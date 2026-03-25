<?php
namespace Magecomp\Sms\Observer\Customer;

use Magento\Framework\Event\ObserverInterface;

class CreditmemoSaveObserver implements ObserverInterface
{
    protected $helperapi;
    protected $helpercreditmemo;
    protected $emailfilter;
    protected $customerFactory;
    protected $orderdata;
    protected $pricehelper;

    public function __construct(
        \Magecomp\Sms\Helper\Apicall $helperapi,
        \Magecomp\Sms\Helper\Creditmemo $helpercreditmemo,
        \Magento\Email\Model\Template\Filter $filter,
        \Magento\Sales\Model\Order $orderdata,
        \Magento\Framework\Pricing\Helper\Data $pricehelper,
        \Magento\Customer\Model\CustomerFactory $customerFactory)
    {
        $this->helperapi = $helperapi;
        $this->helpercreditmemo = $helpercreditmemo;
        $this->emailfilter = $filter;
        $this->orderdata = $orderdata;
        $this->pricehelper = $pricehelper;
        $this->customerFactory = $customerFactory;
    }

    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        if(!$this->helpercreditmemo->isEnabled())
            return $this;

        $creditmemo = $observer->getCreditmemo();
        $order      = $creditmemo->getOrder();

        $curr_order = $this->orderdata->load($order->getId());

        $creditmemoCollection = $curr_order->getCreditmemosCollection();

        if($creditmemo)
        {
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
                    'creditmemo' => $creditmemo,
                    'customer' => $customer,
                    'mobilenumber' => $mobilenumber
                ]);
            }
            else
            {
                $this->emailfilter->setVariables([
                    'order' => $order,
                    'creditmemo' => $creditmemo,
                    'mobilenumber' => $mobilenumber
                ]);
            }

            if ($this->helpercreditmemo->isCreditmemoNotificationForUser($order->getStoreId()))
            {
                $message = $this->helpercreditmemo->getCreditmemoNotificationUserTemplate($order->getStoreId());

                $orderTotal=$this->pricehelper->currency($creditmemo->getGrandTotal(), true, false);
                $codes = array('{{var creditmemo.grand_total}}');
                $accurate = array($orderTotal);

                $message=str_replace($codes,$accurate,$message);

                $finalmessage = $this->emailfilter->filter($message);
                $this->helperapi->callApiUrl($mobilenumber,$finalmessage);
            }

            if($this->helpercreditmemo->isCreditmemoNotificationForAdmin($order->getStoreId()) && $this->helpercreditmemo->getAdminNumber())
            {
                $message = $this->helpercreditmemo->getCreditmemoNotificationForAdminTemplate($order->getStoreId());
                $finalmessage = $this->emailfilter->filter($message);
                $this->helperapi->callApiUrl($this->helpercreditmemo->getAdminNumber(),$finalmessage);
            }
        }
        return $this;
    }
}
