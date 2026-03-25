<?php
namespace Magecomp\Recentsalesnotification\Helper;
use Magento\Framework\App\Action\Context;
use \Magento\Framework\App\Helper\AbstractHelper;
use Magento\Store\Model\ScopeInterface;

class Data extends AbstractHelper
{
    const SELECTEDTEMPLATE = 'recentsalesnotification/layoutsetting/template';
    const BGIMAGE = 'recentsalesnotification/layoutsetting/bgimage';
    const BGCOLOR = 'recentsalesnotification/layoutsetting/bgcolor';
    const LAYOUT = 'recentsalesnotification/layoutsetting/layout';
    const TEXTCOLOR = 'recentsalesnotification/general/textcolor';
    const ICONCOLOR = 'recentsalesnotification/general/iconcolor';
    const AJAXCALLTIME = 'recentsalesnotification/general/ajaxcalltime';
    const MESSAGE = 'recentsalesnotification/general/message';
    const POSITION = 'recentsalesnotification/general/position';
    const NOOFORDER = 'recentsalesnotification/general/nooforder';

    const FAKEORDERENABLE = 'recentsalesnotification/ordersetting/orderenable';
    const FAKEORDERCUSTOMERNAME = 'recentsalesnotification/ordersetting/customername';
    const FAKEORDERPRODUCTID = 'recentsalesnotification/ordersetting/productid';
    const FAKEORDERSHIPPINGADDRESS = 'recentsalesnotification/ordersetting/shippingaddress';
    const FAKEORDERORDERTIME = 'recentsalesnotification/ordersetting/ordertime';

    public function getFakeOrderEnabled(){
        return $this->scopeConfig->getValue(
            self::FAKEORDERENABLE,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }
    public function getFakeOrderCustomerName(){
        $arr=explode(",", $this->scopeConfig->getValue(self::FAKEORDERCUSTOMERNAME, ScopeInterface::SCOPE_STORE));
        $random_keys=array_rand($arr,1);
        return $arr[$random_keys];
    }
    public function getFakeOrderProductId(){
        $arr=explode(",", $this->scopeConfig->getValue(self::FAKEORDERPRODUCTID, ScopeInterface::SCOPE_STORE));
        $random_keys=array_rand($arr,1);
        return $arr[$random_keys];
    }
    public function getFakeOrderShippingAddress(){
        $arr=explode(",", $this->scopeConfig->getValue(self::FAKEORDERSHIPPINGADDRESS, ScopeInterface::SCOPE_STORE));
        $random_keys=array_rand($arr,1);
        return $arr[$random_keys];
    }
    public function getFakeOrderTime()
    {
        $arr = explode(",", $this->scopeConfig->getValue(self::FAKEORDERORDERTIME, ScopeInterface::SCOPE_STORE));
        $random_keys = array_rand($arr, 1);
        return $arr[$random_keys];
    }
    public function getNoOfOrder()
    {
        return $this->scopeConfig->getValue(
            self::NOOFORDER,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    public function getPosition()
    {
        return $this->scopeConfig->getValue(
            self::POSITION,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    public function getNotificationText()
    {
        return $this->scopeConfig->getValue(
            self::MESSAGE,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    public function getAjaxcalltime()
    {
        return $this->scopeConfig->getValue(
            self::AJAXCALLTIME,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    public function getIconcolor()
    {
        return $this->scopeConfig->getValue(
            self::ICONCOLOR,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    public function getTextcolor()
    {
        return $this->scopeConfig->getValue(
            self::TEXTCOLOR,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    public function getLayout()
    {
        return $this->scopeConfig->getValue(
            self::LAYOUT,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    public function getBgcolor()
    {
        return $this->scopeConfig->getValue(
            self::BGCOLOR,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    public function getBgimage()
    {
        return $this->scopeConfig->getValue(
            self::BGIMAGE,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    public function getSelectedTemplate()
    {
        return $this->scopeConfig->getValue(
            self::SELECTEDTEMPLATE,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

}