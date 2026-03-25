<?php 
namespace Magecomp\Sms\Helper;

use Magento\Store\Model\ScopeInterface;

class Shipment extends \Magecomp\Sms\Helper\Data
{
    // USER TEMPLATE
    const SMS_IS_CUSTOMER_SHIPMENT_NOTIFICATION = 'usertemplate/usershipment/enable';
    const SMS_CUSTOMER_SHIPMENT_NOTIFICATION_TEMPLATE = 'usertemplate/usershipment/template';

	//ADMIN TEMPLATE
    const SMS_IS_ADMIN_SHIPMENT_NOTIFICATION = 'admintemplate/adminshipment/enable';
    const SMS_ADMIN_SHIPMENT_NOTIFICATION_TEMPLATE = 'admintemplate/adminshipment/template';

    public function isShipmentNotificationForUser($storeid) {
        return $this->isEnabled() && $this->scopeConfig->getValue(self::SMS_IS_CUSTOMER_SHIPMENT_NOTIFICATION,
            ScopeInterface::SCOPE_STORE,
            $storeid);
    }

    public function getShipmentNotificationUserTemplate($storeid)
    {
        if($this->isEnabled())
        {
            return  $this->scopeConfig->getValue(self::SMS_CUSTOMER_SHIPMENT_NOTIFICATION_TEMPLATE,
                ScopeInterface::SCOPE_STORE,
                $storeid);
        }
    }

    public function isShipmentNotificationForAdmin($storeid)
    {
        return $this->isEnabled() && $this->scopeConfig->getValue(self::SMS_IS_ADMIN_SHIPMENT_NOTIFICATION,
            ScopeInterface::SCOPE_STORE,
            $storeid);
    }

    public function getShipmentNotificationForAdminTemplate($storeid)
    {
        if($this->isEnabled())
        {
            return  $this->scopeConfig->getValue(self::SMS_ADMIN_SHIPMENT_NOTIFICATION_TEMPLATE,
                ScopeInterface::SCOPE_STORE,
                $storeid);
        }
    }
}