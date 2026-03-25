<?php
namespace Magecomp\Recentsalesnotification\Block;

class Recentsalesnotification extends \Magento\Framework\View\Element\Template
{
    public $_orderCollectionFactory;
    public $context;
    protected $_productloader;
    protected $storeManger;
    protected $_countryFactory;
    protected $helperdata;
    protected $imageHelper;

    public function __construct(
        \Magento\Sales\Model\OrderFactory $orderCollectionFactory,
        \Magento\Catalog\Model\ProductFactory $_productloader,
        \Magento\Directory\Model\CountryFactory $countryFactory,
        \Magento\Framework\View\Element\Template\Context $context,
        \Magecomp\Recentsalesnotification\Helper\Data $helperdata,
        \Magento\Store\Model\StoreManagerInterface $storeManger,
        \Magento\Catalog\Helper\Image $imageHelper
    )
    {
        $this->_productloader = $_productloader;
        $this->_orderCollectionFactory = $orderCollectionFactory;
        $this->_countryFactory = $countryFactory;
        $this->helperdata = $helperdata;
        $this->imageHelper = $imageHelper;
        $this->storeManger = $storeManger;
        parent::__construct($context);
    }


    public function getOrderNotification()
    {

        $LastOrder = $this->_orderCollectionFactory->create()->getCollection()->getLastItem();
        $Order = $this->_orderCollectionFactory->create()->load($LastOrder->getId());
        $message = $this->helperdata->getNotificationText();
        $position = $this->helperdata->getPosition();
        $textcolor = $this->helperdata->getTextcolor();
        $iconcolor = $this->helperdata->getIconcolor();
        $layout = $this->helperdata->getLayout();
        $bgcolor = $this->helperdata->getBgcolor();
        $bgimage = $this->helperdata->getBgimage();

        $currentStore = $this->storeManger->getStore();
        $baseUrl = $this->storeManger->getStore()->getBaseUrl();
        $mediaUrl = $currentStore->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA);

        $backImage = $mediaUrl . 'recentsalesnotification/backgroundimage/' . $bgimage;

        $proName = array();
        $customerName = array();
        $productUrl = array();
        $createdAt = $Order->getCreatedAt();

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

            foreach ($Order->getAllItems() as $item) {
                $ProdustIds[] = $item->getProductId();
                $proName = $item->getName(); // product name
                $customerName = $Order->getCustomerFirstname() . ' ' . $Order->getCustomerLastname();

                $product = $this->_productloader->create()->load($item->getProductId());
                $image = 'category_page_list';
                $image_url = $this->imageHelper->init($product, $image)->keepFrame(TRUE)->keepAspectRatio(TRUE)->resize(200, 199)->getUrl();
                $productUrl = $product->getProductUrl();
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
        $message = $this->messageConvert($proName, $customerName, $countryName, $message, $hourdiff, $city);

        $html = "";
        $html .= '<div class="recent_sales_notificationtext ' . $position . '" id="' . $LastOrder->getId() . '">';
        $html .= '<span class="closenotification"><i class="fa fa-close"></i></span>';
        $html .= '<div class="recent_salesnotificationcontent" id="Recentsalesnotificationcontent">';
        $html .= '<div class="recent_salesnotificationimg">';
        $html .= '    	<a class="producturl" href="' . $productUrl . '"  target="_blank"><img src="' . $image_url . '" class="v-middle" alt="" height="100px" width="100px"/></a>';
        $html .= '    </div>';
        $html .= '    <div class="recent_salesnotificationtext">';
        $html .= '    	<span>' . $message . '</span>';
        $html .= '        <div class="recent_salesnotificationtext1">';
        $html .= '    	    ' .$hourdiff. '';
        $html .= '        </div>';
        $html .= '    </div>';
        $html .= '</div>';

        $html .= "<style>
		.recent_sales_notificationtext{color:" . $textcolor . "}
		.fa.fa-close{color:" . $iconcolor . "}";

        if ($layout == 'solidcolor') {
            $html .= ".recent_sales_notificationtext{background-color:" . $bgcolor . "}";
        } else if ($layout == 'image') {
            $html .= ".recent_sales_notificationtext{background:url(" . $backImage . ")}";
        } else if ($layout == 'template') {
            $selectedTemplate = $this->helperdata->getSelectedTemplate();
            $imageUrl = $this->getViewFileUrl('Magecomp_Recentsalesnotification::images/' . $selectedTemplate . '.png');
            $html .= ".recent_sales_notificationtext{background:url(" . $imageUrl . ")}";
            if ($selectedTemplate == 'template2' || $selectedTemplate == 'template7') {
                $html .= ".recent_salesnotificationcontent{margin-top: 45px;}
			        .closenotification{right: 6px;top: 47px;}";
            }
        }


        $html .= "</style>";

        $html .= '</div>';

        return $html;
    }

    public function messageConvert($proName, $customerName, $countryName, $message, $hourdiff, $city)
    {
        $codes = array('{{product_name}}', '{{customer_name}}', '{{country_name}}', '{{hour}}', '{{city}}');
        $accurate = array($proName, $customerName, $countryName, $hourdiff, $city);
        return str_replace($codes, $accurate, $message);
    }
    public function getNoOfOrder()
    {
        return $this->helperdata->getNoOfOrder();
    }
    public function getAjaxcalltime()
    {
        return $this->helperdata->getAjaxcalltime();
    }


}