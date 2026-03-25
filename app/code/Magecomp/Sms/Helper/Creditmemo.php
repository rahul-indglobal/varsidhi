<?php 
namespace Magecomp\Sms\Helper;

use Magento\Store\Model\ScopeInterface;

class Creditmemo extends \Magecomp\Sms\Helper\Data
{
    // USER TEMPLATE
    const SMS_IS_CUSTOMER_CREDITMEMO_NOTIFICATION = 'usertemplate/usercreditmemo/enable';
    const SMS_CUSTOMER_CREDITMEMO_NOTIFICATION_TEMPLATE = 'usertemplate/usercreditmemo/template';

	//ADMIN TEMPLATE
    const SMS_IS_ADMIN_CREDITMEMO_NOTIFICATION = 'admintemplate/admincreditmemo/enable';
    const SMS_ADMIN_CREDITMEMO_NOTIFICATION_TEMPLATE = 'admintemplate/admincreditmemo/template';

    public function isCreditmemoNotificationForUser($storeid) {
        return $this->isEnabled() && $this->scopeConfig->getValue(self::SMS_IS_CUSTOMER_CREDITMEMO_NOTIFICATION,
            ScopeInterface::SCOPE_STORE,
            $storeid);
    }

    public function getCreditmemoNotificationUserTemplate($storeid)
    {
        if($this->isEnabled())
        {
            return  $this->scopeConfig->getValue(self::SMS_CUSTOMER_CREDITMEMO_NOTIFICATION_TEMPLATE,
                ScopeInterface::SCOPE_STORE,
                $storeid);
        }
    }

    public function isCreditmemoNotificationForAdmin($storeid)
    {
        return $this->isEnabled() && $this->scopeConfig->getValue(self::SMS_IS_ADMIN_CREDITMEMO_NOTIFICATION,
            ScopeInterface::SCOPE_STORE,
            $storeid);
    }

    public function getCreditmemoNotificationForAdminTemplate($storeid)
    {
        if($this->isEnabled())
        {
            return  $this->scopeConfig->getValue(self::SMS_ADMIN_CREDITMEMO_NOTIFICATION_TEMPLATE,
                ScopeInterface::SCOPE_STORE,
                $storeid);
        }
    }
}