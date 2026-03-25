<?php
namespace Custom\PincodeChecker\Controller\Index;
use Magento\Framework\Controller\ResultFactory; 
class Zipcode extends \Magento\Framework\App\Action\Action
{

    /**
     * @var \Custom\PincodeChecker\Helper\Data
     */
    protected $helper;

    protected $messageManager;

     /**
     * Constructor
     *
     * @param \Magento\Framework\App\Action\Context $context
     * @param \Custom\PincodeChecker\Helper\Data $helper
     * @param \Magento\Framework\Controller\Result\JsonFactory $resultPageFactory
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Custom\PincodeChecker\Helper\Data $helper,
        \Magento\Framework\Message\ManagerInterface $messageManager
    ) {
        parent::__construct($context);
        $this->helper = $helper;
        $this->messageManager = $messageManager;
        
    }

    /**
     * Execute view action
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {   

        $post = $this->getRequest()->getPostValue();
       
        $zipcode = $post['zipcode'];
         
        $pincodeStatus = $this->helper->getPincodeStatus($zipcode);
        
        $resultJson = $this->resultFactory->create(ResultFactory::TYPE_JSON);
        if($pincodeStatus){  
                
            $result = "Cash On Delivery available in ".$pincodeStatus." for this zipcode.";
            $resultJson->setData($result);
            return $resultJson;
        }
        else{
            $resultJson->setData("");
            return $resultJson;
        }



    }

}