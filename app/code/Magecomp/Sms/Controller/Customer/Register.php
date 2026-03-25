<?php
namespace Magecomp\Sms\Controller\Customer;
 
use Magento\Framework\App\Action\Context;
use Magento\Framework\Controller\ResultFactory;

class Register extends \Magento\Framework\App\Action\Action
{
    protected $helperapi;
    protected $helpercustomer;
    protected $smsmodel;
    protected $emailfilter;
    protected $registercustomer;
    protected $customerRepositoryInterface;

    public function __construct(Context $context,
                                \Magecomp\Sms\Helper\Apicall $helperapi,
                                \Magecomp\Sms\Helper\Customer $helpercustomer,
                                \Magento\Customer\Model\Customer $customer,
                                \Magento\Customer\Api\CustomerRepositoryInterface $customerRepositoryInterface,
                                \Magecomp\Sms\Model\SmsFactory $smsmodel,
                                \Magento\Email\Model\Template\Filter $filter)
    {
        $this->helperapi = $helperapi;
        $this->helpercustomer = $helpercustomer;
        $this->smsmodel = $smsmodel;
        $this->registercustomer = $customer;
        $this->customerRepositoryInterface = $customerRepositoryInterface;
        $this->emailfilter = $filter;
        parent::__construct($context);
    }

    public function execute()
    {
        try
        {
            $mobilenumber = $this->getRequest()->getParam('mobile');
            $otp = $this->helpercustomer->getOtp();
            $customerCollection= $this->registercustomer->getCollection();
            foreach ($customerCollection as $customer)
            {
                $cust = $this->customerRepositoryInterface->getById($customer->getId());
                $MobValue = $cust->getCustomAttribute('mobilenumber');

                if ($MobValue)
                {
                    $mobile = $MobValue->getValue();
                    if($mobilenumber==$mobile)
                    {
                        $data = array(__("There is already an account with this mobile number."));
                        $resultJson = $this->resultFactory->create(ResultFactory::TYPE_JSON);
                        $resultJson->setData($data);
                        return $resultJson;
                    }
                }
            }


            $this->emailfilter->setVariables(['otp' => $otp]);
            $message = $this->helpercustomer->getSignUpConfirmationUserTemplate();
            $finalmessage = $this->emailfilter->filter($message);

            $responce = $this->helperapi->callApiUrl($mobilenumber,$finalmessage);
            if($responce === true)
            {
                $smsModel = $this->smsmodel->create();
                $smscollection = $smsModel->getCollection();
                $smscollection->addFieldToFilter('mobile_number', $mobilenumber);

                if(count($smscollection)>0)
                {
                    $smsModel = $this->smsmodel->create()->load($mobilenumber,'mobile_number');
                }
                $smsModel->setMobileNumber($mobilenumber)
                        ->setOtp($otp)
                        ->setIsverify(0)
                        ->save();

                $data = array("success");
                $resultJson = $this->resultFactory->create(ResultFactory::TYPE_JSON);
                $resultJson->setData($data);
                return $resultJson;
            }
            else
            {
                $resultJson = $this->resultFactory->create(ResultFactory::TYPE_JSON);
                $resultJson->setData($responce);
                return $resultJson;
            }
        }
        catch (\Exception $e) {
            $data = array("error");
            $resultJson = $this->resultFactory->create(ResultFactory::TYPE_JSON);
            $resultJson->setData($data);
            return $resultJson;
        }
    }
}