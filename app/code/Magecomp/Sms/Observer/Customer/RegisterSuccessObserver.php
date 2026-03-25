<?php
namespace Magecomp\Sms\Observer\Customer;

use Magento\Framework\Event\ObserverInterface;

class RegisterSuccessObserver implements ObserverInterface
{
    protected $helperapi;
    protected $helpercustomer;
    protected $smsmodel;
    protected $emailfilter;
    protected $registercustomer;
    protected $customerFactory;

    public function __construct(
        \Magecomp\Sms\Helper\Apicall $helperapi,
        \Magecomp\Sms\Helper\Customer $helpercustomer,
        \Magento\Customer\Model\Customer $customer,
        \Magento\Customer\Model\ResourceModel\CustomerFactory $customerFactory,
        \Magecomp\Sms\Model\SmsFactory $smsmodel,
        \Magento\Email\Model\Template\Filter $filter)
    {
        $this->helperapi = $helperapi;
        $this->helpercustomer = $helpercustomer;
        $this->registercustomer = $customer;
        $this->customerFactory = $customerFactory;
        $this->smsmodel = $smsmodel;
        $this->emailfilter = $filter;
    }

    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        if(!$this->helpercustomer->isEnabled())
            return $this;
        $customer = $observer->getEvent()->getCustomer();

        $controller = $observer->getAccountController();
        $countrycode=$controller->getRequest()->getParam('countryreg');
        $mobilenumber =$countrycode.$controller->getRequest()->getParam('mobilenumber');
        $tempcustomer = $this->registercustomer->load($customer->getId());

        $this->emailfilter->setVariables([
            'customer' => $tempcustomer,
            'mobilenumber' => $mobilenumber
        ]);

        if($this->helpercustomer->isSignUpNotificationForAdmin() && $this->helpercustomer->getAdminNumber())
        {
            $message = $this->helpercustomer->getSignUpNotificationForAdminTemplate();
            $finalmessage = $this->emailfilter->filter($message);
            $this->helperapi->callApiUrl($this->helpercustomer->getAdminNumber(),$finalmessage);
        }

        if($mobilenumber == '' || $mobilenumber == null)
            return $this;

        $smsModel = $this->smsmodel->create();
        $smscollection = $smsModel->getCollection()
                       ->addFieldToFilter('mobile_number', $mobilenumber);
        foreach ($smscollection as $sms)
        {
            $cust = $this->registercustomer->load($customer->getId());
            $customerData = $cust->getDataModel();
            $customerData->setCustomAttribute('mobilenumber', $mobilenumber);
            $cust->updateData($customerData);

            $this->customerFactory->create()->saveAttribute($cust, 'mobilenumber');
            $cust->save();
            $sms->delete();
        }

        if ($this->helpercustomer->isSignUpNotificationForUser())
        {
            $message = $this->helpercustomer->getSignUpNotificationForUserTemplate();
            $finalmessage = $this->emailfilter->filter($message);
            $this->helperapi->callApiUrl($mobilenumber,$finalmessage);
        }
        return $this;
    }
}
