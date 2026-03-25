<?php

namespace Magecomp\Recentsalesnotification\Controller\Index;

use Magento\Framework\App\Action\Context;
use Magento\Framework\Controller\ResultFactory;

class Getorderdetail extends \Magento\Framework\App\Action\Action
{
    public $_orderCollectionFactory;
    protected $_productloader;
    protected $scopeConfig;
    protected $_countryFactory;
    protected $helperdata;
    protected $imageHelper;

    public function __construct(
        Context $context,
        \Magento\Sales\Model\OrderFactory $orderCollectionFactory,
        \Magento\Catalog\Model\ProductFactory $_productloader,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Directory\Model\CountryFactory $countryFactory,
        \Magecomp\Recentsalesnotification\Helper\Data $helperdata,
        \Magento\Catalog\Helper\Image $imageHelper
    )
    {
        $this->_productloader = $_productloader;
        $this->_orderCollectionFactory = $orderCollectionFactory;
        $this->scopeConfig = $scopeConfig;
        $this->_countryFactory = $countryFactory;
        $this->helperdata = $helperdata;
        $this->imageHelper = $imageHelper;
        parent::__construct($context);
    }

    public function execute()
    {
        $Order = $this->_orderCollectionFactory->create()->load($this->getRequest()->getParam('orderId'));
        $createdAt = $Order->getCreatedAt();
        $proName = array();
        $customerName = array();
        $productUrl = array();
        $message=$this->helperdata->getNotificationText();
        if ($this->helperdata->getFakeOrderEnabled()) {
            $hourdiff = $this->helperdata->getFakeOrderTime();
            $customerName = $this->helperdata->getFakeOrderCustomerName();
            $countryName = $this->helperdata->getFakeOrderShippingAddress();
            $city = '';
            $product_id = $this->helperdata->getFakeOrderProductId();
            $product = $this->_productloader->create()->load($product_id);
            $proName = $product->getName();
            $image = 'category_page_list';
            $image_url = $this->imageHelper->init($product, $image)->keepFrame(TRUE)->keepAspectRatio(TRUE)->resize(199, 200)->getUrl();
            $productUrl = $product->getProductUrl();
        } else {
            foreach ($Order->getAllItems() as $item) {
                $ProdustIds[] = $item->getProductId();
                $proName = $item->getName(); // product name
                $customerName = $Order->getCustomerFirstname() . ' ' . $Order->getCustomerLastname();
                $product = $this->_productloader->create()->load($item->getProductId());
                $image = 'category_page_list';
                $image_url = $this->imageHelper->init($product, $image)->keepFrame(TRUE)->keepAspectRatio(TRUE)->resize(199, 200)->getUrl();
                $productUrl = $product->getProductUrl();
            }
            if (floor(((time() - strtotime($createdAt)) / 3600) / 24) >= 1) {
                $hourdiff = floor(((time() - strtotime($createdAt)) / 3600) / 24) . __(" Days ago");
            } else if (floor((time() - strtotime($createdAt)) / 3600) != 0) {
                $hourdiff = floor((time() - strtotime($createdAt)) / 3600) . __(" Hours ago");
            } else {
                if (floor((time() - strtotime($createdAt)) / 60) == 0) {
                    $hourdiff = __(" A Few Min Ago");
                } else {
                    $hourdiff = floor((time() - strtotime($createdAt)) / 60) . __(" Min Ago");
                }
            }
            $data = $Order->getBillingAddress()->getData();
            $country = $this->_countryFactory->create()->loadByCode($data['country_id']);
            $countryName = $country->getName();
            $city = '';
            if ($Order->getBillingAddress()->getCity()) {
                $city = $Order->getBillingAddress()->getCity();
            } else {
                $city = $Order->getShippingAddress()->getCity();
            }
        }

        $codes = array('{{product_name}}', '{{customer_name}}', '{{country_name}}', '{{hour}}', '{{city}}');
        $accurate = array($proName, $customerName, $countryName, $hourdiff, $city);
        $message = str_replace($codes, $accurate, $message);
        $response['img'] = $image_url;
        $response['producturl'] = $productUrl;
        $response['message'] = $message;

        $resultJson = $this->resultFactory->create(ResultFactory::TYPE_JSON);
        $resultJson->setData($response);
        return $resultJson;
    }
}