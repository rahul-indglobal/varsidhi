<?php
namespace Magecomp\Sms\Observer\Customer;

use Magento\Framework\Event\ObserverInterface;

class CancelorderSaveObserver implements ObserverInterface
{
    protected $helperapi;
    protected $helperorder;
    protected $emailfilter;
    protected $customerFactory;

    public function __construct(
        \Magecomp\Sms\Helper\Apicall $helperapi,
        \Magecomp\Sms\Helper\Cancelorder $helperorder,
        \Magento\Email\Model\Template\Filter $filter,
        \Magento\Customer\Model\CustomerFactory $customerFactory)
    {
        $this->helperapi = $helperapi;
        $this->helperorder = $helperorder;
        $this->emailfilter = $filter;
        $this->customerFactory = $customerFactory;
    }

    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        if(!$this->helperorder->isEnabled() && !$this->helperorder->isCancelorderNotificationForUser())
            return $this;

        $order = $observer->getEvent()->getOrder();
        if($order)
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
                    'customer' => $customer,
                    'order_total' => $order->formatPriceTxt($order->getGrandTotal()),
                    'mobilenumber' => $mobilenumber
                ]);
            }
            else
            {
                $this->emailfilter->setVariables([
                    'order' => $order,
                    'order_total' => $order->formatPriceTxt($order->getGrandTotal()),
                    'mobilenumber' => $mobilenumber
                ]);
            }
            if ($this->helperorder->isCancelorderNotificationForUser())
            {
                $message = $this->helperorder->getCancelorderNotificationUserTemplate();
                $finalmessage = $this->emailfilter->filter($message);
                $this->helperapi->callApiUrl($mobilenumber,$finalmessage);
            }
        }
        return $this;
    }
}
