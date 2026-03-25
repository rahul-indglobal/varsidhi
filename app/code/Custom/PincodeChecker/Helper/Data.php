<?php
namespace Custom\PincodeChecker\Helper;
use Magento\Framework\Controller\ResultFactory;
class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
    /**
     * @var \Custom\PincodeChecker\Model\ResourceModel\Pincodechecker\CollectionFactory
     */
    protected $pincodeCollection;

    /**
     * @var \Magento\Catalog\Model\Product
     */
    protected $product;

    /**
     * @var \Magento\Framework\Controller\ResultFactory
     */
    protected $resultFactory;

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * Constructor
     *
     * @param \Magento\Framework\App\Helper\Context $context
     * @param \Magento\Catalog\Model\Product $product
     * @param \Custom\PincodeChecker\Model\ResourceModel\Pincodechecker\CollectionFactory $pincodeCollection
     * @param \Magento\Framework\Controller\ResultFactory $resultFactory
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Catalog\Model\Product $product,
        \Custom\PincodeChecker\Model\ResourceModel\Pincodechecker\CollectionFactory $pincodeCollection,
        \Magento\Framework\Controller\ResultFactory $resultFactory,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
    )
    {
        $this->pincodeCollection = $pincodeCollection;
        $this->product = $product;
        $this->resultFactory = $resultFactory;
        $this->scopeConfig = $scopeConfig;
        parent::__construct($context);
    }

    /**
     * Get collection of pincode
     */
    public function getCollection()
    {
        return $this->pincodeCollection->create();
    }

    /**
     * Get pincode status
     */
    public function getPincodeStatus($pincode)
    {   
        $collection = $this->getCollection();
        $collection->addFieldToFilter('pincode', array('eq' => $pincode)); 
        foreach($collection as $item){
      
        $pincodeStatus = $item->getDeliveryStatus();   
        
        }

        if($collection->getData()){
            return $pincodeStatus;
        }else{
            return false;
        }

    }

    /**
     * Get pincode status by product
     */
    public function getProductPincodeStatus($id, $pincode)
    {
        $product = $this->product->load($id);
        $pincodes = $product->getData('pincode');
        $pincodeArr = explode(',', $pincodes);

        if(in_array($pincode, $pincodeArr))
        {
            return true;
        }else{
            return false;
        }
            
    }

    /**
     * Get pincode status message
     */
    public function getMessage($status, $pincode)
    {
        if($status){
            $message = "<h3 style='color:red'> ✔️COD is Available on this product <br> <h5>Product WIll Be Delivered Within ". $status.".</h5></h3>";
			$status = 1;
        }else{
            $message = "<h3 style='color:red'>".$this->getFailMessage()."</h3>";
			$status = 0;
        }

        return ['message' => $message, 'status' => $status];
    }

    /**
     * Get pincode cod status 
     */
    public function getCashondelivery($pincode)
    {  
        $collection = $this->getCollection();
        $collection->addFieldToFilter('pincode', array('eq' => $pincode)); 
        foreach($collection as $item){
      
        $cod = $item->getCod();   
        
        }

        if($collection->getData()){
            return $cod;
        }
    }

    /**
     * Get pincode shipping status 
     */
    public function getShipping($pincode)
    {  
        $collection = $this->getCollection();
        $collection->addFieldToFilter('pincode', array('eq' => $pincode)); 
        foreach($collection as $item){
      
        $cod = $item->getShipping();   
        
        }

        if($collection->getData()){
            return $cod;
        }
    }
    /**
     * Get redirect url
     */
    public function getRedirect()
    {
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        $resultRedirect->setUrl($this->_redirect->getRefererUrl());
        return $resultRedirect;
    }

    /**
     * Check module enable
     */
    public function getIsEnable()
    {
        return $this->scopeConfig->getValue('pincode/general/active', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
    }

    /**
     * Get check on addtocart config value
     */
    public function getIsCheckonAddtoCart()
    {
        return $this->scopeConfig->getValue('pincode/general/checkaddtocart', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
    }

    /**
     * Get success message config value
     */
    public function getSuccessMessage()
    {
        return $this->scopeConfig->getValue('pincode/general/successmessage', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
    }

    /**
     * Get fail message config value
     */
    public function getFailMessage()
    {
        return $this->scopeConfig->getValue('pincode/general/failmessage', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
    }
}